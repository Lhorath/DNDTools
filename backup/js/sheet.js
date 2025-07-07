/*
    Dack's DND Tools - js/sheet.js
    ==============================
    This file contains all JavaScript logic for the interactive Character Sheet page.
    It handles data saving/loading to localStorage, dynamic stat calculations, API lookups
    for items/spells, and PDF generation.
*/

document.addEventListener('DOMContentLoaded', () => {
    // SECTION 1: INITIALIZATION
    // ===========================
    // This function sets up the entire character sheet.
    const initCharacterSheet = () => {
        const characterSheetPage = document.getElementById('character-sheet');
        if (!characterSheetPage) return; // Exit if not on the character sheet page.

        // --- Core Functions (Data Management & Calculations) ---

        const saveData = () => {
            const data = {};
            characterSheetPage.querySelectorAll('input, textarea, select').forEach(input => {
                if (input.id) {
                    data[input.id] = input.type === 'checkbox' ? input.checked : input.value;
                }
            });

            const getDynamicItems = (containerId) => {
                const items = [];
                document.querySelectorAll(`#${containerId} [data-item-url]`).forEach(card => {
                    items.push({
                        url: card.dataset.itemUrl,
                        isCustom: card.dataset.isCustom === 'true',
                        customData: card.dataset.isCustom === 'true' ? JSON.parse(card.dataset.customData) : null
                    });
                });
                return items;
            };

            data.equipmentCards = getDynamicItems('equipment-cards-container');
            data.spellCards = getDynamicItems('spell-cards-container');
            data.treasureCards = getDynamicItems('treasure-cards-container');

            localStorage.setItem('dndCharacterData', JSON.stringify(data));
            showNotification('Character data saved!');
        };

        const loadData = () => {
            const data = JSON.parse(localStorage.getItem('dndCharacterData'));
            if (!data) {
                updateAllCalculations();
                return;
            }

            characterSheetPage.querySelectorAll('input, textarea, select').forEach(input => {
                if (data[input.id] !== undefined) {
                     if (input.tagName.toLowerCase() === 'select') {
                        setTimeout(() => {
                            input.value = data[input.id];
                            if(input.id === 'class' || input.id === 'race') {
                                 input.dispatchEvent(new Event('change'));
                            }
                        }, 700);
                    }
                    else if (input.type === 'checkbox') {
                        input.checked = data[input.id];
                    } else {
                        input.value = data[input.id];
                    }
                }
            });

            const loadDynamicItems = (savedItems, createCardFn) => {
                if (savedItems && Array.isArray(savedItems)) {
                    savedItems.forEach(item => {
                        if (item.isCustom) {
                            createCardFn(item.customData);
                        } else if (item.url) {
                            fetch(item.url)
                                .then(res => res.json())
                                .then(apiData => {
                                    if (apiData.results && apiData.results.length > 0) {
                                        createCardFn(apiData.results[0]);
                                    } else if (apiData && !apiData.results) {
                                        createCardFn(apiData);
                                    }
                                });
                        }
                    });
                }
            };

            loadDynamicItems(data.equipmentCards, createEquipmentCard);
            loadDynamicItems(data.spellCards, createSpellCard);
            loadDynamicItems(data.treasureCards, createTreasureCard);

            setTimeout(updateAllCalculations, 1200);
        };

        const updateAllCalculations = () => {
            const mods = {};
            document.querySelectorAll('.ability-score').forEach(input => {
                const score = parseInt(input.value, 10) || 10;
                const mod = Math.floor((score - 10) / 2);
                mods[input.id] = mod;
                const modElement = document.getElementById(input.dataset.mod);
                if (modElement) modElement.value = mod >= 0 ? `+${mod}` : mod;
            });

            const level = parseInt(document.getElementById('level')?.value, 10) || 1;
            const profBonus = Math.ceil(level / 4) + 1;
            const profBonusInput = document.getElementById('proficiency-bonus');
            if (profBonusInput) profBonusInput.value = `+${profBonus}`;

            document.querySelectorAll('#character-sheet .prof-check').forEach(checkbox => {
                const isSave = checkbox.id.includes('-save-');
                const baseId = isSave ? checkbox.id.replace('-save-prof', '') : checkbox.id.replace('-prof', '');
                const targetInput = document.getElementById(isSave ? `${baseId}-save` : baseId);
                const ability = checkbox.dataset.ability;
                if (targetInput && ability && mods[ability] !== undefined) {
                    const total = checkbox.checked ? mods[ability] + profBonus : mods[ability];
                    targetInput.value = total >= 0 ? `+${total}` : total;
                }
            });

            const passivePerceptionInput = document.getElementById('passive-perception');
            if (passivePerceptionInput && mods.wisdom !== undefined) {
                const perceptionProf = document.getElementById('perception-prof')?.checked;
                passivePerceptionInput.value = 10 + mods.wisdom + (perceptionProf ? profBonus : 0);
            }

            const initiativeInput = document.getElementById('initiative');
            if (initiativeInput && mods.dexterity !== undefined) initiativeInput.value = mods.dexterity >= 0 ? `+${mods.dexterity}` : mods.dexterity;

            const spellAbilityInput = document.getElementById('spell-ability');
            if (spellAbilityInput) {
                const spellAbility = spellAbilityInput.value;
                const spellAbilityMod = mods[spellAbility];
                if(spellAbilityMod !== undefined) {
                    const spellSaveDC = document.getElementById('spell-save-dc');
                    const spellAtkBonus = document.getElementById('spell-atk-bonus');
                    if (spellSaveDC) spellSaveDC.value = 8 + profBonus + spellAbilityMod;
                    if (spellAtkBonus) spellAtkBonus.value = profBonus + spellAbilityMod;
                }
            }
        };

        // --- UI & Helper Functions ---

        const showNotification = (message) => {
            let notification = document.getElementById('notification');
            if (!notification) {
                notification = document.createElement('div');
                notification.id = 'notification';
                document.body.appendChild(notification);
            }
            notification.textContent = message;
            notification.classList.add('show');
            setTimeout(() => notification.classList.remove('show'), 3000);
        };
        const escapeHTML = (str) => {
            if (typeof str !== 'string') return '';
            return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        };

        // --- Dynamic Content (Search, Modals, Cards) ---

        const populateDropdown = async (elementId, category) => {
            const selectElement = document.getElementById(elementId);
            if (!selectElement) return;
            selectElement.innerHTML = '<option>Loading...</option>';
            try {
                const response = await fetch(`api/search.php?category=${category}`);
                const data = await response.json();
                if (data.error || !data.results) throw new Error(data.error || `Failed to load ${category}`);
                selectElement.innerHTML = '<option value="">Select...</option>';
                data.results.sort((a, b) => a.name.localeCompare(b.name)).forEach(item => {
                    const option = new Option(item.name, item.index);
                    selectElement.add(option);
                });
            } catch (error) {
                selectElement.innerHTML = '<option>Error loading</option>';
            }
        };

        const handleClassChange = async (event) => {
            const classIndex = event.target.value;
            if (!classIndex) return;
            try {
                const response = await fetch(`api/search.php?category=classes&index=${classIndex}`);
                const data = (await response.json()).results[0];
                if (document.getElementById('hit-dice')) document.getElementById('hit-dice').value = `1d${data.hit_die}`;
                document.querySelectorAll('input[id$="-save-prof"]').forEach(cb => cb.checked = false);
                data.saving_throws?.forEach(save => {
                    const checkbox = document.getElementById(`${save.index}-save-prof`);
                    if(checkbox) checkbox.checked = true;
                });
                if(data.spellcasting) {
                    document.getElementById('spell-class').value = data.name;
                    document.getElementById('spell-ability').value = data.spellcasting.spellcasting_ability.name.slice(0, 3).toLowerCase();
                } else {
                     document.getElementById('spell-class').value = '';
                     document.getElementById('spell-ability').value = '';
                }
                updateAllCalculations();
            } catch (error) {
                console.error('Failed to handle class change:', error);
            }
        };
        const handleRaceChange = async (event) => {
            const raceIndex = event.target.value;
            if (!raceIndex) return;
            try {
                const response = await fetch(`api/search.php?category=races&index=${raceIndex}`);
                const data = (await response.json()).results[0];
                if (document.getElementById('speed')) document.getElementById('speed').value = data.speed;
            } catch (error) {
                console.error('Failed to handle race change:', error);
            }
        };

        function setupSearchableList(options) {
            const searchInput = document.getElementById(options.searchInputId);
            const searchBtn = document.getElementById(options.searchBtnId);
            const resultsContainer = document.getElementById(options.searchResultsId);
            const cardsContainer = document.getElementById(options.cardsContainerId);
            const listContainer = document.getElementById(options.listId);

            if (!searchInput || !searchBtn || !resultsContainer || !cardsContainer || !listContainer) return { createCard: () => {} };

            const searchApi = async () => {
                const query = searchInput.value.trim().toLowerCase();
                if (!query) { resultsContainer.innerHTML = ''; return; }
                resultsContainer.innerHTML = `<div class="search-results-list"><p class="p-2 text-muted">Searching...</p></div>`;
                try {
                    const response = await fetch(`api/search.php?category=${options.apiEndpoint}&query=${encodeURIComponent(query)}`);
                    const data = await response.json();
                    if (data.error || !data.results || data.results.length === 0) throw new Error(data.error || 'No results found');

                    const listHtml = data.results.map(item =>
                        `<li><button class="w-full text-left p-2 hover:bg-bg-panel add-item-btn" data-item='${escapeHTML(JSON.stringify(item))}'>${escapeHTML(item.name)}</button></li>`
                    ).join('');
                    resultsContainer.innerHTML = `<div class="search-results-list"><ul>${listHtml}</ul></div>`;
                } catch (error) {
                    resultsContainer.innerHTML = `<div class="search-results-list"><p class="p-2 text-red-500">Error: ${error.message}</p></div>`;
                }
            };

            const addToList = (name, url) => {
                const existingListItem = listContainer.querySelector(`[data-url="${url}"]`);
                if(existingListItem) return; // Prevent adding duplicates to the list

                const itemEl = document.createElement('div');
                itemEl.className = `${options.itemClass} flex justify-between items-center p-1`;
                itemEl.dataset.url = url;
                itemEl.innerHTML = `<span>${escapeHTML(name)}</span><button class="remove-from-list-btn text-red-500 hover:text-red-300 transition-colors">&times;</button>`;
                listContainer.appendChild(itemEl);
            };

            const createCard = (data) => {
                const url = data.isCustom ? `custom-${Date.now()}` : `api/search.php?category=${options.apiEndpoint}&index=${data.index}`;
                const existingCard = cardsContainer.querySelector(`[data-item-url="${url}"]`);
                if (existingCard) {
                    showNotification(`${data.name} is already in your list.`);
                    return;
                }
                const card = document.createElement('div');
                card.className = `section relative ${options.cardClass}`;
                card.dataset.itemUrl = url;
                card.dataset.isCustom = data.isCustom || false;
                if (data.isCustom) card.dataset.customData = JSON.stringify(data);
                card.innerHTML = `<button class="absolute top-2 right-2 remove-card-btn text-red-500 text-xl hover:text-red-300 transition-colors">&times;</button>${options.cardContentFn(data)}`;
                cardsContainer.appendChild(card);
                addToList(data.name, url); // Also add to the simple list
            };

            searchBtn.addEventListener('click', searchApi);
            searchInput.addEventListener('keyup', e => e.key === 'Enter' && searchApi());
            resultsContainer.addEventListener('click', e => {
                if (e.target.classList.contains('add-item-btn')) {
                    const itemData = JSON.parse(e.target.dataset.item);
                    createCard(itemData);
                    searchInput.value = '';
                    resultsContainer.innerHTML = '';
                }
            });

            // Centralized event listener for removing items from list OR card view
            document.body.addEventListener('click', e => {
                const listEl = e.target.closest(`.${options.itemClass}`);
                if (e.target.classList.contains('remove-from-list-btn') && listEl) {
                     const card = cardsContainer.querySelector(`[data-item-url="${listEl.dataset.url}"]`);
                     if (card) card.remove();
                     listEl.remove();
                }
                const cardEl = e.target.closest(`#${options.cardsContainerId} .${options.cardClass}`);
                 if (e.target.classList.contains('remove-card-btn') && cardEl) {
                     const listItem = listContainer.querySelector(`[data-url="${cardEl.dataset.itemUrl}"]`);
                     if(listItem) listItem.remove();
                     cardEl.remove();
                 }
            });
            return { createCard };
        }

        const { createCard: createEquipmentCard } = setupSearchableList({
            searchInputId: 'equipment-search', searchBtnId: 'equipment-search-btn', searchResultsId: 'equipment-search-results', listId: 'equipment-list', cardsContainerId: 'equipment-cards-container', apiEndpoint: 'equipment', itemClass: 'equipment-item', cardClass: 'equipment-card',
            cardContentFn: (data) => `<h3 class="section-title pr-6">${escapeHTML(data.name)}</h3><p class="text-sm">${escapeHTML(data.desc || (Array.isArray(data.description) ? data.description.join(' ') : ''))}</p>`
        });
        const { createCard: createSpellCard } = setupSearchableList({
            searchInputId: 'spell-search', searchBtnId: 'spell-search-btn', searchResultsId: 'spell-search-results', listId: 'spell-list', cardsContainerId: 'spell-cards-container', apiEndpoint: 'spells', itemClass: 'spell-item', cardClass: 'spell-card',
            cardContentFn: (data) => `<h3 class="section-title pr-6">${escapeHTML(data.name)}</h3><p class="text-sm">${escapeHTML(Array.isArray(data.desc) ? data.desc.join(' ') : (data.description || ''))}</p>`
        });
        const { createCard: createTreasureCard } = setupSearchableList({
            searchInputId: 'treasure-search', searchBtnId: 'treasure-search-btn', searchResultsId: 'treasure-search-results', listId: 'treasure-list', cardsContainerId: 'treasure-cards-container', apiEndpoint: 'magic_items', itemClass: 'treasure-item', cardClass: 'treasure-card',
            cardContentFn: (data) => `<h3 class="section-title pr-6">${escapeHTML(data.name)}</h3><p class="text-sm">${escapeHTML(Array.isArray(data.desc) ? data.desc.join(' ') : '')}</p>`
        });

        const openCustomItemModal = (type) => {
            const modalContainer = document.createElement('div');
            modalContainer.id = 'custom-item-modal';
            let title = `Add Custom ${type.charAt(0).toUpperCase() + type.slice(1)}`;
            modalContainer.innerHTML = `
                <div class="bg-panel p-6 rounded-lg shadow-xl w-full max-w-md section">
                    <h3 class="section-title">${title}</h3>
                    <form id="custom-item-form" class="space-y-4 mt-4">
                        <input type="text" id="custom-name" placeholder="Item Name" required>
                        <textarea id="custom-desc" placeholder="Description..." rows="4" required></textarea>
                        <div class="flex justify-end gap-4">
                            <button type="button" id="cancel-custom-item" class="action-button bg-gray-600 hover:bg-gray-700 font-bold py-2 px-4">Cancel</button>
                            <button type="submit" class="action-button font-bold py-2 px-4">Save</button>
                        </div>
                    </form>
                </div>`;
            document.body.appendChild(modalContainer);

            modalContainer.querySelector('#cancel-custom-item').addEventListener('click', () => modalContainer.remove());
            modalContainer.querySelector('#custom-item-form').addEventListener('submit', (e) => {
                e.preventDefault();
                const customData = { isCustom: true, name: e.target.querySelector('#custom-name').value, desc: e.target.querySelector('#custom-desc').value, description: e.target.querySelector('#custom-desc').value };
                if (type === 'spell') createSpellCard(customData);
                if (type === 'equipment') createEquipmentCard(customData);
                if (type === 'treasure') createTreasureCard(customData);
                modalContainer.remove();
            });
        };

        const downloadPDF = async () => {
            const { jsPDF } = window.jspdf;
            const sheetContent = document.getElementById('character-sheet');
            if (!sheetContent || !window.html2canvas) {
                showNotification("PDF generation library not loaded yet.");
                return;
            }
            showNotification("Generating PDF... Please wait.");
            try {
                const canvas = await html2canvas(sheetContent, { scale: 2, backgroundColor: '#111111', useCORS: true });
                const imgData = canvas.toDataURL('image/png');
                const pdf = new jsPDF({ orientation: 'p', unit: 'pt', format: 'a4' });
                const pdfWidth = pdf.internal.pageSize.getWidth();
                const pdfHeight = pdf.internal.pageSize.getHeight();
                const imgWidth = canvas.width;
                const imgHeight = canvas.height;
                const ratio = imgWidth / imgHeight;
                const canvasPdfWidth = pdfWidth;
                const canvasPdfHeight = canvasPdfWidth / ratio;

                let totalPdfHeight = canvasPdfHeight;
                let yPosition = 0;

                if (totalPdfHeight < pdfHeight) {
                    pdf.addImage(imgData, 'PNG', 0, 0, canvasPdfWidth, canvasPdfHeight);
                } else {
                    let pageCount = Math.ceil(totalPdfHeight / pdfHeight);
                    for (let i = 0; i < pageCount; i++) {
                        pdf.addImage(imgData, 'PNG', 0, yPosition, canvasPdfWidth, totalPdfHeight);
                        yPosition -= pdfHeight;
                        if (i < pageCount - 1) {
                            pdf.addPage();
                        }
                    }
                }
                pdf.save("dnd-character-sheet.pdf");
            } catch(e) {
                showNotification("Error generating PDF.");
                console.error(e);
            }
        };

        const inputsToWatch = document.querySelectorAll('.ability-score, #level, .prof-check');
        inputsToWatch.forEach(el => el.addEventListener('change', updateAllCalculations));
        document.getElementById('class')?.addEventListener('change', handleClassChange);
        document.getElementById('race')?.addEventListener('change', handleRaceChange);
        document.getElementById('save-button')?.addEventListener('click', saveData);
        document.getElementById('download-pdf')?.addEventListener('click', downloadPDF);
        document.getElementById('add-custom-spell')?.addEventListener('click', () => openCustomItemModal('spell'));
        document.getElementById('add-custom-equipment')?.addEventListener('click', () => openCustomItemModal('equipment'));
        document.getElementById('add-custom-treasure')?.addEventListener('click', () => openCustomItemModal('treasure'));

        populateDropdown('race', 'races');
        populateDropdown('class', 'classes');
        loadData();
    };

    initCharacterSheet();
});
