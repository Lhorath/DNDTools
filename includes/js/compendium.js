/*
    Dack's DND Tools - includes/js/compendium.js
    ============================================
    This file contains all JavaScript logic for the Compendium page.
    This version has been completely refactored to work with the new,
    normalized database schema and the updated search API.
*/

document.addEventListener('DOMContentLoaded', () => {
    // --- SECTION 1: INITIALIZATION & ELEMENT SELECTION ---
    // =====================================================
    // Exit if we're not on the compendium page.
    const lookupTool = document.getElementById('lookup-tool');
    if (!lookupTool) return;

    // Cache all necessary DOM elements for performance.
    const searchInput = document.getElementById('lookup-search-input');
    const searchButton = document.getElementById('lookup-search-button');
    const resultsContainer = document.getElementById('lookup-results');
    const paginationControls = document.getElementById('pagination-controls');
    const categoryNav = document.getElementById('compendium-categories');

    // --- SECTION 2: STATE MANAGEMENT ---
    // ===================================
    // Variables to keep track of the user's current view.
    let currentCategory = 'spells';
    let currentPage = 1;
    let currentQuery = '';

    // --- SECTION 3: HELPER & UTILITY FUNCTIONS ---
    // ===========================================
    // Small, reusable functions for common tasks.
    const escapeHTML = (str) => {
        if (typeof str !== 'string' && typeof str !== 'number') return '';
        return String(str).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    };

    const formatKey = (key) => key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    
    const renderDetailRow = (label, value) => {
        if (!value && value !== 0) return '';
        return `<div><strong class="title-font">${escapeHTML(label)}:</strong> ${escapeHTML(String(value))}</div>`;
    };
    
    const renderDescription = (desc) => {
        if (!desc) return '';
        const descHtml = Array.isArray(desc) ? desc.map(p => `<p>${escapeHTML(p)}</p>`).join('') : `<p>${escapeHTML(desc)}</p>`;
        return `<hr class="my-4 border-border-color"><div class="space-y-4">${descHtml}</div>`;
    };

    const renderListSection = (title, items, key = 'name') => {
        if (!items || items.length === 0) return '';
        const listItems = Array.isArray(items) ? items.map(item => `<li class="ml-4 list-disc">${escapeHTML(item?.[key] || item)}</li>`).join('') : `<li class="ml-4 list-disc">${escapeHTML(items)}</li>`;
        return `<p class="mt-2"><strong class="title-font">${title}:</strong></p><ul>${listItems}</ul>`;
    };

    const formatArrayField = (field, formatter = (item => escapeHTML(item.name || item))) => {
        if (!field || field.length === 0) return 'None';
        if (Array.isArray(field)) {
            return field.map(formatter).join(', ') || 'None';
        }
        return escapeHTML(String(field));
    };
    
    // --- SECTION 4: CARD RENDERING FUNCTIONS ---
    // ===========================================
    // This object maps a category name to its specific rendering function,
    // making the display logic clean and scalable.
    const renderers = {
        spells: (data) => {
            const spellLevel = data.spell_level === 0 ? 'Cantrip' : `Level ${data.spell_level}`;
            const classes = formatArrayField(data.classes);
            const subclasses = formatArrayField(data.subclasses);
            return `
                <div class="text-lg">
                    <p class="text-muted">${escapeHTML(data.school_index)} ${spellLevel}</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 mt-4">
                        ${renderDetailRow('Casting Time', data.casting_time)}
                        ${renderDetailRow('Range', data.spell_range)}
                        ${renderDetailRow('Components', data.components)}
                        ${renderDetailRow('Duration', data.duration)}
                    </div>
                    ${data.material ? `<p class="mt-1"><strong class="title-font">Material:</strong> ${escapeHTML(data.material)}</p>`: ''}
                    ${renderDescription(data.description)}
                    ${data.higher_level ? `<div class="mt-4"><strong class="title-font">At Higher Levels:</strong><p>${escapeHTML(data.higher_level)}</p></div>` : ''}
                    <hr class="my-4 border-border-color">
                    <p><strong class="title-font">Classes:</strong> ${classes}</p>
                    <p><strong class="title-font">Subclasses:</strong> ${subclasses}</p>
                </div>
            `;
        },
        monsters: (data) => {
            const abilityScores = `
                <div class="grid grid-cols-3 md:grid-cols-6 gap-2 text-center my-4">
                    <div><strong class="title-font">STR</strong><p>${data.strength} (${Math.floor((data.strength - 10) / 2)})</p></div>
                    <div><strong class="title-font">DEX</strong><p>${data.dexterity} (${Math.floor((data.dexterity - 10) / 2)})</p></div>
                    <div><strong class="title-font">CON</strong><p>${data.constitution} (${Math.floor((data.constitution - 10) / 2)})</p></div>
                    <div><strong class="title-font">INT</strong><p>${data.intelligence} (${Math.floor((data.intelligence - 10) / 2)})</p></div>
                    <div><strong class="title-font">WIS</strong><p>${data.wisdom} (${Math.floor((data.wisdom - 10) / 2)})</p></div>
                    <div><strong class="title-font">CHA</strong><p>${data.charisma} (${Math.floor((data.charisma - 10) / 2)})</p></div>
                </div>`;
            const proficiencies = formatArrayField(data.proficiencies, p => `${escapeHTML(p.name)} +${p.value}`);
            const conditionImmunities = formatArrayField(data.condition_immunities);
            const senses = data.senses ? Object.entries(data.senses).map(([key, value]) => `${formatKey(key)} ${value}`).join(', ') : 'None';
            const speed = data.speed ? Object.entries(data.speed).map(([key, value]) => `${key} ${value}`).join(', ') : 'None';
            const renderActionSection = (title, items) => {
                if (!items || items.length === 0) return '';
                const content = items.map(item => `<div><strong class="title-font">${escapeHTML(item.name)}:</strong> <p>${escapeHTML(item.desc || (Array.isArray(item.description) ? item.description.join(' ') : ''))}</p></div>`).join('');
                return `<h3 class="text-2xl title-font mt-4 border-t-2 border-border-color pt-4">${title}</h3><div class="space-y-3 mt-2">${content}</div>`;
            };
            return `
                <p class="text-muted">${escapeHTML(data.size)} ${escapeHTML(data.type)}, ${escapeHTML(data.alignment)}</p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-x-8 gap-y-2 mt-4 text-center sm:text-left">
                    <div><strong class="title-font">Armor Class:</strong> ${data.armor_class?.[0]?.value || ''} ${data.armor_class?.[0]?.type ? `(${data.armor_class[0].type})` : ''}</div>
                    <div><strong class="title-font">Hit Points:</strong> ${data.hit_points} (${data.hit_dice})</div>
                    <div><strong class="title-font">Speed:</strong> ${speed}</div>
                </div>
                <hr class="my-2 border-border-color">
                ${abilityScores}
                <hr class="my-2 border-border-color">
                <div class="text-sm space-y-1">
                    <p><strong class="title-font">Proficiencies:</strong> ${proficiencies}</p>
                    <p><strong class="title-font">Damage Vulnerabilities:</strong> ${formatArrayField(data.damage_vulnerabilities, v => escapeHTML(v))}</p>
                    <p><strong class="title-font">Damage Resistances:</strong> ${formatArrayField(data.damage_resistances, r => escapeHTML(r))}</p>
                    <p><strong class="title-font">Damage Immunities:</strong> ${formatArrayField(data.damage_immunities, i => escapeHTML(i))}</p>
                    <p><strong class="title-font">Condition Immunities:</strong> ${conditionImmunities}</p>
                    <p><strong class="title-font">Senses:</strong> ${senses}</p>
                    <p><strong class="title-font">Languages:</strong> ${escapeHTML(data.languages)}</p>
                    <p><strong class="title-font">Challenge:</strong> ${data.challenge_rating} (${data.xp} XP)</p>
                </div>
                ${renderActionSection('Special Abilities', data.special_abilities)}
                ${renderActionSection('Actions', data.actions)}
                ${renderActionSection('Legendary Actions', data.legendary_actions)}
            `;
        },
        equipment: (data) => {
            let cost = data.cost ? `${data.cost.quantity} ${data.cost.unit}` : 'N/A';
            let properties = formatArrayField(data.properties);
            let damage = data.damage ? `${data.damage.damage_dice} ${data.damage.damage_type.name}`: null;
            let armor_class = data.armor_class ? `Base ${data.armor_class.base}` + (data.armor_class.dex_bonus ? ' + Dex bonus' : '') + (data.armor_class.max_bonus ? ` (max ${data.armor_class.max_bonus})` : '') : null;
            return `
                <p class="text-muted">${escapeHTML(data.equipment_category_index)}</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 mt-4">
                    ${renderDetailRow('Cost', cost)}
                    ${renderDetailRow('Weight', data.weight ? `${data.weight} lb.` : 'N/A')}
                    ${renderDetailRow('Damage', damage)}
                    ${renderDetailRow('Armor Class', armor_class)}
                    ${renderDetailRow('Properties', properties)}
                    ${renderDetailRow('STR Minimum', data.str_minimum)}
                    ${renderDetailRow('Stealth', data.stealth_disadvantage ? 'Disadvantage' : 'Normal')}
                </div>
                ${renderDescription(data.description)}
            `;
        },
        magic_items: (data) => {
             return `
                <p class="text-muted">${escapeHTML(data.equipment_category_index)} (${escapeHTML(data.rarity_name)})</p>
                 ${renderDescription(data.description)}
            `;
        },
        classes: (data) => {
            const proficiencies = formatArrayField(data.proficiencies);
            const savingThrows = formatArrayField(data.saving_throws);
            return `
                <p class="text-muted">Class Features</p>
                 <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 mt-4">
                    ${renderDetailRow('Hit Die', `d${data.hit_die}`)}
                    ${renderDetailRow('Saving Throws', savingThrows)}
                 </div>
                 <hr class="my-4 border-border-color">
                 <p><strong class="title-font">Proficiencies:</strong> ${proficiencies}</p>
                 ${data.spellcasting ? `<p class="mt-2"><strong class="title-font">Spellcasting Ability:</strong> ${escapeHTML(data.spellcasting.spellcasting_ability.name)}</p>` : ''}
                 ${renderListSection('Subclasses', data.subclasses)}
            `;
        },
        races: (data) => {
            const abilityBonuses = formatArrayField(data.ability_bonuses, b => `${b.ability_score.name} +${b.bonus}`);
            const languages = formatArrayField(data.languages);
            return `
                <p class="text-muted">${escapeHTML(data.size)} Humanoid</p>
                 <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 mt-4">
                    ${renderDetailRow('Speed', `${data.speed} ft.`)}
                    ${renderDetailRow('Ability Bonuses', abilityBonuses)}
                 </div>
                 <hr class="my-4 border-border-color">
                 <p><strong class="title-font">Languages:</strong> ${languages}</p>
                 ${renderDescription(data.alignment)}
                 ${renderDescription(data.age)}
                 ${renderDescription(data.size_description)}
                 ${renderListSection('Traits', data.traits)}
            `;
        },
        backgrounds: (data) => {
            return `
                <p class="text-muted">Character Background</p>
                ${renderDescription(data.feature_desc)}
                <hr class="my-4 border-border-color">
                ${renderListSection('Starting Proficiencies', data.starting_proficiencies)}
                ${renderListSection('Ideals', data.ideals, 'desc')}
                ${renderListSection('Bonds', data.bonds, 'desc')}
                ${renderListSection('Flaws', data.flaws, 'desc')}
            `;
        },
        feats: (data) => {
            const prereqs = formatArrayField(data.prerequisites, p => `${p.ability_score.name} ${p.minimum_score}`);
            return `
                <p class="text-muted">Feat</p>
                <p><strong class="title-font">Prerequisites:</strong> ${prereqs}</p>
                ${renderDescription(data.description)}
            `;
        },
        generic: (data) => {
            let content = '';
            for (const [key, value] of Object.entries(data)) {
                if (key === 'id' || key === 'index' || key === 'name' || !value || key === 'url') continue;
                let formattedValue = Array.isArray(value) 
                    ? value.map(i => escapeHTML(i.name || i)).join(', ')
                    : typeof value === 'object' ? '' : escapeHTML(value);
                if (formattedValue) {
                    content += `<p class="mt-2"><strong class="title-font">${formatKey(key)}:</strong> ${formattedValue}</p>`;
                }
            }
            return content || '<p class="text-muted">No additional details available.</p>';
        }
    };

    // --- MAIN DISPLAY FUNCTIONS ---
    const displayDetail = (data) => {
        const renderer = renderers[currentCategory] || renderers.generic;
        const cardContent = renderer(data);
        const detailHtml = `
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-4xl title-font">${escapeHTML(data.name)}</h2>
                <button id="back-to-list-btn" class="action-button py-1 px-3 text-sm">Back to List</button>
            </div>
            <div class="border-b-2 border-accent-red mb-4"></div>
            ${cardContent}
        `;
        resultsContainer.innerHTML = detailHtml;
        paginationControls.innerHTML = '';
    };

    const displayResultsList = (matches) => {
        const listHtml = matches.map(item =>
            `<li><button class="result-item-btn w-full text-left p-3 rounded-md hover:bg-bg-main border border-transparent hover:border-color" data-item='${escapeHTML(JSON.stringify(item))}'>${escapeHTML(item.name)}</button></li>`
        ).join('');
        resultsContainer.innerHTML = `<ul class="space-y-2">${listHtml}</ul>`;
    };
    
    const renderPagination = (pagination) => {
        if (!pagination || pagination.total_pages <= 1) {
            paginationControls.innerHTML = '';
            return;
        }
        let buttons = '';
        const maxPagesToShow = 5;
        const startPage = Math.max(1, pagination.current_page - Math.floor(maxPagesToShow / 2));
        const endPage = Math.min(pagination.total_pages, startPage + maxPagesToShow - 1);
        if (pagination.current_page > 1) buttons += `<button class="pagination-btn" data-page="${pagination.current_page - 1}">&laquo; Prev</button>`;
        if (startPage > 1) buttons += `<button class="pagination-btn" data-page="1">1</button>`;
        if (startPage > 2) buttons += `<span class="pagination-ellipsis">...</span>`;
        for (let i = startPage; i <= endPage; i++) {
            buttons += i === pagination.current_page
                ? `<span class="pagination-btn active-page">${i}</span>`
                : `<button class="pagination-btn" data-page="${i}">${i}</button>`;
        }
        if (endPage < pagination.total_pages - 1) buttons += `<span class="pagination-ellipsis">...</span>`;
        if (endPage < pagination.total_pages) buttons += `<button class="pagination-btn" data-page="${pagination.total_pages}">${pagination.total_pages}</button>`;
        if (pagination.current_page < pagination.total_pages) buttons += `<button class="pagination-btn" data-page="${pagination.current_page + 1}">Next &raquo;</button>`;
        paginationControls.innerHTML = buttons;
    };

    const fetchAndDisplayList = async (category, page = 1, query = '') => {
        resultsContainer.innerHTML = `<p class="text-muted text-center p-8">Fetching data...</p>`;
        paginationControls.innerHTML = '';
        try {
            const response = await fetch(`includes/core/search.php?category=${category}&page=${page}&query=${encodeURIComponent(query)}`);
            if (!response.ok) throw new Error('Network response was not ok.');
            const data = await response.json();
            if (data.error || !data.results) throw new Error(data.error || 'No results found.');
            if (data.results.length === 0) {
                 resultsContainer.innerHTML = `<p class="text-muted text-center p-8">No results found.</p>`;
            } else {
                displayResultsList(data.results);
            }
            renderPagination(data.pagination);
        } catch (error) {
            resultsContainer.innerHTML = `<p class="text-red-500 text-center font-semibold p-8">${error.message}</p>`;
        }
    };

    // --- EVENT LISTENERS ---
    const setupEventListeners = () => {
        searchButton.addEventListener('click', () => {
            currentPage = 1;
            currentQuery = searchInput.value;
            fetchAndDisplayList(currentCategory, currentPage, currentQuery);
        });

        searchInput.addEventListener('keyup', (event) => {
            if (event.key === 'Enter') searchButton.click();
        });

        resultsContainer.addEventListener('click', (event) => {
            const itemButton = event.target.closest('.result-item-btn');
            if (itemButton) {
                try {
                    const itemData = JSON.parse(itemButton.getAttribute('data-item'));
                    fetch(`includes/core/search.php?category=${currentCategory}&index=${itemData.index}`)
                        .then(res => res.json())
                        .then(detailData => {
                            if (detailData.results && detailData.results.length > 0) {
                                displayDetail(detailData.results[0]);
                            } else {
                                throw new Error("Detailed data not found.");
                            }
                        });
                } catch(e) {
                    console.error("Failed to parse or fetch item data:", e);
                    resultsContainer.innerHTML = `<p class="text-red-500 text-center font-semibold p-8">Could not load item details.</p>`;
                }
            }
            if (event.target.id === 'back-to-list-btn') {
                fetchAndDisplayList(currentCategory, currentPage, currentQuery);
            }
        });

        paginationControls.addEventListener('click', (event) => {
            if (event.target.classList.contains('pagination-btn') && event.target.dataset.page) {
                currentPage = parseInt(event.target.dataset.page, 10);
                fetchAndDisplayList(currentCategory, currentPage, currentQuery);
            }
        });

        categoryNav.addEventListener('click', (event) => {
            if (event.target.tagName === 'BUTTON') {
                categoryNav.querySelector('.active-sheet-tab')?.classList.remove('active-sheet-tab');
                event.target.classList.add('active-sheet-tab');
                currentCategory = event.target.dataset.tab;
                searchInput.placeholder = `Search for ${currentCategory.replace(/_/g, ' ')}...`;
                currentQuery = '';
                searchInput.value = '';
                currentPage = 1;
                fetchAndDisplayList(currentCategory, currentPage);
            }
        });
    };

    // --- INITIAL LOAD ---
    setupEventListeners();
    fetchAndDisplayList(currentCategory, currentPage);
});
