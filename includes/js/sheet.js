/*
    Dack's DND Tools - includes/js/sheet.js
    ======================================
    This is the complete and final version for REV 012. It removes all local storage
    functionality and correctly handles all dynamic data loading from the new schema,
    including the fix for the subclass and subrace dropdown issues.
*/

document.addEventListener('DOMContentLoaded', () => {
    
    const initCharacterSheet = async () => {
        const characterSheetPage = document.getElementById('character-sheet');
        if (!characterSheetPage) return;

        // --- SECTION 1: CORE FUNCTIONS & CALCULATIONS ---

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
                    if (spellAtkBonus) spellAtkBonus.value = `+${profBonus + spellAbilityMod}`;
                }
            }
        };

        // --- SECTION 2: UI & HELPER FUNCTIONS ---
        const showNotification = (message, isError = false) => {
            let notification = document.getElementById('notification');
            if (!notification) {
                notification = document.createElement('div');
                notification.id = 'notification';
                document.body.appendChild(notification);
            }
            notification.textContent = message;
            notification.className = 'show';
            if(isError) notification.classList.add('bg-red-800');
            setTimeout(() => notification.className = notification.className.replace('show', ''), 3000);
        };
        const escapeHTML = (str) => {
            if (typeof str !== 'string') return '';
            return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        };
        const renderDescription = (desc) => {
            if (!desc) return '';
            const descHtml = Array.isArray(desc) ? desc.map(p => `<p class="text-sm">${escapeHTML(p)}</p>`).join('') : `<p class="text-sm">${escapeHTML(desc)}</p>`;
            return `<div class="mt-2 text-muted">${descHtml}</div>`;
        };

        // --- SECTION 3: DYNAMIC CONTENT & API INTERACTIONS ---
        const populateDropdown = async (elementId, category, options = []) => {
            const selectElement = document.getElementById(elementId);
            if (!selectElement) return Promise.resolve();
            selectElement.innerHTML = '<option>Loading...</option>';
            try {
                let items = options;
                if (category) {
                    const response = await fetch(`includes/core/search.php?category=${category}`);
                    const data = await response.json();
                    if (data.error || !data.results) throw new Error(data.error || `Failed to load ${category}`);
                    items = data.results;
                }
                selectElement.innerHTML = '<option value="">Select...</option>';
                items.sort((a, b) => a.name.localeCompare(b.name)).forEach(item => {
                    const option = new Option(item.name, item.index);
                    selectElement.add(option);
                });
            } catch (error) {
                selectElement.innerHTML = '<option>Error loading</option>';
            }
        };

        const clearAutoAddedItems = (source) => {
            document.querySelectorAll(`[data-auto-added="${source}"]`).forEach(el => {
                if (el.tagName === 'INPUT' && el.type === 'checkbox') {
                    el.checked = false;
                } else if (el.tagName === 'TEXTAREA') {
                    const lines = el.value.split('\n');
                    const filteredLines = lines.filter(line => !line.startsWith(`[Auto-${source}]`));
                    el.value = filteredLines.join('\n');
                } else if (el.tagName === 'INPUT' || el.tagName === 'SELECT') {
                    el.value = '';
                } else {
                    el.remove();
                }
            });
            document.querySelectorAll(`.equipment-card[data-source="${source}"]`).forEach(card => card.remove());
        };

        const openChoiceModal = (choiceData, onConfirm) => {
            const modalContainer = document.createElement('div');
            modalContainer.id = 'choice-modal';
            modalContainer.className = 'fixed inset-0 bg-black/70 flex items-center justify-center z-50';
            const optionsHtml = choiceData.options?.from.map(opt => `
                <div class="flex items-center">
                    <input type="checkbox" id="choice-${opt.item.index}" value="${opt.item.index}" data-name="${opt.item.name}" class="choice-check h-4 w-4 me-2">
                    <label for="choice-${opt.item.index}">${opt.item.name}</label>
                </div>
            `).join('');

            if (!optionsHtml) { return; }

            modalContainer.innerHTML = `
                <div class="bg-panel p-6 rounded-lg shadow-xl w-full max-w-md section">
                    <h3 class="section-title">${escapeHTML(choiceData.desc)}</h3>
                    <p class="text-muted mb-4">Choose ${choiceData.choose}</p>
                    <form id="choice-form" class="space-y-2">${optionsHtml}</form>
                    <div class="flex justify-end mt-6">
                        <button id="confirm-choice-btn" class="action-button font-bold py-2 px-4">Confirm Selection</button>
                    </div>
                </div>`;
            document.body.appendChild(modalContainer);
            const form = modalContainer.querySelector('#choice-form');
            const confirmBtn = modalContainer.querySelector('#confirm-choice-btn');
            form.addEventListener('change', (event) => {
                const checkedCount = form.querySelectorAll('.choice-check:checked').length;
                if (checkedCount > choiceData.choose) {
                    showNotification(`You can only choose ${choiceData.choose} options.`, true);
                    event.target.checked = false;
                }
            });
            confirmBtn.addEventListener('click', () => {
                const selections = [];
                form.querySelectorAll('.choice-check:checked').forEach(checkbox => {
                    selections.push({ index: checkbox.value, name: checkbox.dataset.name });
                });
                onConfirm(selections);
                modalContainer.remove();
            });
        };

        const handleClassChange = async (event) => {
            clearAutoAddedItems('class');
            const classIndex = event.target.value;
            const subclassWrapper = document.getElementById('subclass-wrapper');
            if (!classIndex) { subclassWrapper.classList.add('hidden'); return; }

            try {
                const response = await fetch(`includes/core/search.php?category=classes&index=${classIndex}`);
                const data = (await response.json()).results[0];
                
                if (document.getElementById('hit-dice')) { document.getElementById('hit-dice').value = `1d${data.hit_die}`; document.getElementById('hit-dice').dataset.autoAdded = "class"; }
                
                data.saving_throws?.forEach(save => {
                    const checkbox = document.getElementById(`${save.name.toLowerCase()}-save-prof`);
                    if(checkbox) { checkbox.checked = true; checkbox.dataset.autoAdded = "class"; }
                });

                data.proficiency_choices?.forEach(choice => openChoiceModal(choice, (selections) => {
                    selections.forEach(sel => {
                        const skillName = sel.index.replace('skill-', '');
                        const profCheckbox = document.getElementById(`${skillName}-prof`);
                        if (profCheckbox) { profCheckbox.checked = true; profCheckbox.dataset.autoAdded = "class"; }
                    });
                    updateAllCalculations();
                }));

                data.starting_equipment?.forEach(item => createEquipmentCard({ ...item, isAuto: true, source: 'class' }));
                data.starting_equipment_options?.forEach(choice => openChoiceModal(choice, (selections) => {
                    selections.forEach(sel => createEquipmentCard({ index: sel.index, name: sel.name, isAuto: true, source: 'class' }));
                }));

                if (data.subclasses && data.subclasses.length > 0) {
                    populateDropdown('subclass', null, data.subclasses);
                    subclassWrapper.classList.remove('hidden');
                } else {
                    subclassWrapper.classList.add('hidden');
                }
                
                if(data.spellcasting) {
                    document.getElementById('spell-class').value = data.name;
                    document.getElementById('spell-ability').value = data.spellcasting.spellcasting_ability.index;
                } else {
                     document.getElementById('spell-class').value = '';
                     document.getElementById('spell-ability').value = '';
                }
                updateAllCalculations();
            } catch (error) { console.error('Failed to handle class change:', error); }
        };

        const handleRaceChange = async (event) => {
            clearAutoAddedItems('race');
            const raceIndex = event.target.value;
            const subraceWrapper = document.getElementById('subrace-wrapper');
            if (!raceIndex) { subraceWrapper.classList.add('hidden'); return; }
            
            try {
                const response = await fetch(`includes/core/search.php?category=races&index=${raceIndex}`);
                const data = (await response.json()).results[0];
                
                if (document.getElementById('speed')) { document.getElementById('speed').value = data.speed; document.getElementById('speed').dataset.autoAdded = "race"; }

                const otherProfsEl = document.getElementById('other-proficiencies');
                if (otherProfsEl && data.languages) {
                    const langs = data.languages.map(l => l.name).join(', ');
                    otherProfsEl.value += (otherProfsEl.value ? '\n' : '') + `[Auto-race] Languages: ${langs}`;
                }

                const featuresEl = document.getElementById('features-traits');
                if (featuresEl && data.traits) {
                    data.traits.forEach(trait => {
                        featuresEl.value += (featuresEl.value ? '\n' : '') + `[Auto-race] ${trait.name}: ${trait.description[0]}`;
                    });
                }

                if (data.subraces && data.subraces.length > 0) {
                    populateDropdown('subrace', null, data.subraces);
                    subraceWrapper.classList.remove('hidden');
                } else {
                    subraceWrapper.classList.add('hidden');
                }
            } catch (error) { console.error('Failed to handle race change:', error); }
        };
        
        const handleBackgroundChange = async (event) => {
            clearAutoAddedItems('background');
            const backgroundIndex = event.target.value;
            if (!backgroundIndex) return;
            try {
                const response = await fetch(`includes/core/search.php?category=backgrounds&index=${backgroundIndex}`);
                const data = (await response.json()).results[0];
                
                if(data.personality_traits) document.getElementById('personality-traits').value = data.personality_traits;
                if(data.ideals) document.getElementById('ideals').value = data.ideals;
                if(data.bonds) document.getElementById('bonds').value = data.bonds;
                if(data.flaws) document.getElementById('flaws').value = data.flaws;

                const otherProfsEl = document.getElementById('other-proficiencies');
                if (otherProfsEl && data.starting_proficiencies) {
                    const profs = data.starting_proficiencies.map(p => p.name).join(', ');
                    otherProfsEl.value += (otherProfsEl.value ? '\n' : '') + `[Auto-background] Proficiencies: ${profs}`;
                }
                data.starting_equipment?.forEach(item => createEquipmentCard({ index: item.equipment, name: `${item.equipment} (x${item.quantity})`, isAuto: true, source: 'background' }));
            } catch (error) { console.error('Failed to handle background change:', error); }
        };
        
        const handleSubclassChange = async (event) => { /* To be implemented */ };

        const renderSheetSpellCard = (data) => {
            if (data.isCustom) return `<h3 class="section-title pr-6">${escapeHTML(data.name)}</h3>${renderDescription(data.description)}`;
            const spellLevel = data.spell_level === 0 ? 'Cantrip' : `Level ${data.spell_level}`;
            return `
                <h3 class="section-title pr-6">${escapeHTML(data.name)}</h3>
                <p class="text-xs text-muted -mt-2">${escapeHTML(data.school_index)} ${spellLevel}</p>
                ${renderDescription(data.description)}
                ${data.higher_level ? `<div class="mt-2"><strong class="font-bold">At Higher Levels:</strong><p class="text-sm text-muted">${escapeHTML(data.higher_level)}</p></div>` : ''}
            `;
        };

        const renderSheetEquipmentCard = (data) => {
            if (data.isCustom) return `<h3 class="section-title pr-6">${escapeHTML(data.name)}</h3>${renderDescription(data.description)}`;
            let details = `<p class="text-xs text-muted -mt-2">${escapeHTML(data.equipment_category_index)}</p>`;
            if(data.damage) details += `<p class="text-sm font-bold text-light">${data.damage.damage_dice} ${data.damage.damage_type.name}</p>`;
            if(data.armor_class) details += `<p class="text-sm font-bold text-light">AC ${data.armor_class.base}</p>`;
            details += renderDescription(data.description);
            return `<h3 class="section-title pr-6">${escapeHTML(data.name)}</h3>${details}`;
        };
        
        const renderSheetMagicItemCard = (data) => {
            if (data.isCustom) return `<h3 class="section-title pr-6">${escapeHTML(data.name)}</h3>${renderDescription(data.description)}`;
            return `
                <h3 class="section-title pr-6">${escapeHTML(data.name)}</h3>
                <p class="text-xs text-muted -mt-2">${escapeHTML(data.equipment_category_index)} (${escapeHTML(data.rarity_name)})</p>
                ${renderDescription(data.description)}
            `;
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
                    const response = await fetch(`includes/core/search.php?category=${options.apiEndpoint}&query=${encodeURIComponent(query)}`);
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
                if(existingListItem) return;
                const itemEl = document.createElement('div');
                itemEl.className = `${options.itemClass} flex justify-between items-center p-1`;
                itemEl.dataset.url = url;
                itemEl.innerHTML = `<span>${escapeHTML(name)}</span><button class="remove-from-list-btn text-red-500 hover:text-red-300 transition-colors">&times;</button>`;
                listContainer.appendChild(itemEl);
            };

            const createCard = (data) => {
                const url = data.isCustom ? `custom-${Date.now()}` : `includes/core/search.php?category=${options.apiEndpoint}&index=${data.index}`;
                const existingCard = cardsContainer.querySelector(`[data-item-url="${url}"]`);
                if (existingCard) {
                    showNotification(`${data.name} is already on your sheet.`);
                    return;
                }
                const card = document.createElement('div');
                card.className = `section relative ${options.cardClass}`;
                card.dataset.itemUrl = url;
                card.dataset.isCustom = data.isCustom || false;
                card.dataset.autoAdded = data.isAuto || false;
                card.dataset.source = data.source || 'manual';
                if (data.isCustom) card.dataset.customData = JSON.stringify(data);
                card.innerHTML = `<button class="absolute top-2 right-2 remove-card-btn text-red-500 text-xl hover:text-red-300 transition-colors">&times;</button>${options.cardContentFn(data)}`;
                cardsContainer.appendChild(card);
                addToList(data.name, url);
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
            cardContentFn: renderSheetEquipmentCard
        });
        const { createCard: createSpellCard } = setupSearchableList({
            searchInputId: 'spell-search', searchBtnId: 'spell-search-btn', searchResultsId: 'spell-search-results', listId: 'spell-list', cardsContainerId: 'spell-cards-container', apiEndpoint: 'spells', itemClass: 'spell-item', cardClass: 'spell-card',
            cardContentFn: renderSheetSpellCard
        });
        const { createCard: createTreasureCard } = setupSearchableList({
            searchInputId: 'treasure-search', searchBtnId: 'treasure-search-btn', searchResultsId: 'treasure-search-results', listId: 'treasure-list', cardsContainerId: 'treasure-cards-container', apiEndpoint: 'magic_items', itemClass: 'treasure-item', cardClass: 'treasure-card',
            cardContentFn: renderSheetMagicItemCard
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
                const canvas = await html2canvas(sheetContent, { scale: 2, backgroundColor: '#1d1d1d', useCORS: true });
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
                console.error("PDF Generation Error:", e);
            }
        };

        // --- SECTION 4: EVENT LISTENER SETUP ---
        const inputsToWatch = document.querySelectorAll('.ability-score, #level, .prof-check');
        inputsToWatch.forEach(el => el.addEventListener('change', updateAllCalculations));
        document.getElementById('class')?.addEventListener('change', handleClassChange);
        document.getElementById('race')?.addEventListener('change', handleRaceChange);
        document.getElementById('subclass')?.addEventListener('change', handleSubclassChange);
        document.getElementById('background')?.addEventListener('change', handleBackgroundChange);
        document.getElementById('download-pdf')?.addEventListener('click', downloadPDF);
        document.getElementById('add-custom-spell')?.addEventListener('click', () => openCustomItemModal('spell'));
        document.getElementById('add-custom-equipment')?.addEventListener('click', () => openCustomItemModal('equipment'));
        document.getElementById('add-custom-treasure')?.addEventListener('click', () => openCustomItemModal('treasure'));

        // --- SECTION 5: INITIAL PAGE LOAD ---
        await Promise.all([
            populateDropdown('race', 'races'),
            populateDropdown('class', 'classes'),
            populateDropdown('background', 'backgrounds'),
            populateDropdown('alignment', 'alignments')
        ]);
        
        updateAllCalculations();
    };

    initCharacterSheet();
});
