/*
    Dack's DND Tools - js/compendium.js
    ===================================
    This file contains all JavaScript logic for the Compendium page. It handles
    category switching, API searches, pagination, and rendering all data into
    user-friendly, card-style layouts.

    BUG FIX: A robust HTML escaping function is now used to prevent special
    characters from corrupting the JSON data stored in HTML attributes, resolving
    the "could not parse data" error.

    REFACTOR: All card rendering functions have been made resilient to data
    type inconsistencies (e.g., string vs. array) from the database to prevent
    runtime errors.
*/

document.addEventListener('DOMContentLoaded', () => {
    const lookupTool = document.getElementById('lookup-tool');
    if (!lookupTool) return;

    // --- ELEMENT SELECTION ---
    const searchInput = document.getElementById('lookup-search-input');
    const searchButton = document.getElementById('lookup-search-button');
    const resultsContainer = document.getElementById('lookup-results');
    const paginationControls = document.getElementById('pagination-controls');
    const categoryNav = document.getElementById('compendium-categories');

    // --- STATE MANAGEMENT ---
    let currentCategory = 'spells';
    let currentPage = 1;
    let currentQuery = '';

    // --- HELPER & UTILITY FUNCTIONS ---

    const escapeHTML = (str) => {
        if (typeof str !== 'string' && typeof str !== 'number') return '';
        return String(str)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    };

    const formatKey = (key) => key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());

    // --- API & FETCH LOGIC ---
    const fetchAndDisplayList = async (category, page = 1, query = '') => {
        resultsContainer.innerHTML = `<p class="text-muted text-center p-8">Fetching data...</p>`;
        paginationControls.innerHTML = '';

        try {
            const response = await fetch(`api/search.php?category=${category}&page=${page}&query=${encodeURIComponent(query)}`);
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

    // --- RENDER FUNCTIONS ---

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

    const renderDetailRow = (label, value) => {
        if (!value && value !== 0) return '';
        return `<div><strong class="title-font">${escapeHTML(label)}:</strong> ${escapeHTML(String(value))}</div>`;
    };
    const renderDescription = (desc) => {
        if (!desc) return '';
        const descHtml = Array.isArray(desc) ? desc.map(p => `<p>${escapeHTML(p)}</p>`).join('') : `<p>${escapeHTML(desc)}</p>`;
        return `<hr class="border-border-color my-4"><div class="space-y-4">${descHtml}</div>`;
    };
    const renderListSection = (title, items) => {
        if (!items || items.length === 0) return '';
        const listItems = Array.isArray(items) ? items.map(item => `<li class="ml-4 list-disc">${escapeHTML(item?.name || item)}</li>`).join('') : `<li class="ml-4 list-disc">${escapeHTML(items)}</li>`;
        return `<p class="mt-2"><strong class="title-font">${title}:</strong></p><ul>${listItems}</ul>`;
    };
    const formatArrayField = (field, formatter) => {
        if (!field) return 'None';
        if (Array.isArray(field)) {
            return field.map(formatter).join(', ') || 'None';
        }
        return escapeHTML(String(field));
    };

    const renderSpellCard = (data) => {
        const spellLevel = data.spell_level === 0 ? 'Cantrip' : `Level ${data.spell_level}`;
        const classes = formatArrayField(data.classes, c => escapeHTML(c.name));
        return `
            <div class="text-lg">
                <p class="text-muted">${escapeHTML(data.school?.name)} ${spellLevel}</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 mt-4">
                    ${renderDetailRow('Casting Time', data.casting_time)}
                    ${renderDetailRow('Range', data.spell_range)}
                    ${renderDetailRow('Components', Array.isArray(data.components) ? data.components.join(', ') : data.components)}
                    ${renderDetailRow('Duration', data.duration)}
                </div>
                ${data.material ? `<p class="mt-1"><strong class="title-font">Material:</strong> ${escapeHTML(data.material)}</p>`: ''}
                ${renderDescription(data.description)}
                ${data.higher_level ? `<div class="mt-4"><strong class="title-font">At Higher Levels:</strong><p>${Array.isArray(data.higher_level) ? escapeHTML(data.higher_level.join(' ')) : escapeHTML(data.higher_level)}</p></div>` : ''}
                <hr class="border-border-color my-4">
                <p><strong class="title-font">Classes:</strong> ${classes}</p>
            </div>
        `;
    };
    const renderMonsterCard = (data) => {
        const abilityScores = `
            <div class="grid grid-cols-3 md:grid-cols-6 gap-2 text-center my-4">
                <div><strong class="title-font">STR</strong><p>${data.strength} (${Math.floor((data.strength - 10) / 2)})</p></div>
                <div><strong class="title-font">DEX</strong><p>${data.dexterity} (${Math.floor((data.dexterity - 10) / 2)})</p></div>
                <div><strong class="title-font">CON</strong><p>${data.constitution} (${Math.floor((data.constitution - 10) / 2)})</p></div>
                <div><strong class="title-font">INT</strong><p>${data.intelligence} (${Math.floor((data.intelligence - 10) / 2)})</p></div>
                <div><strong class="title-font">WIS</strong><p>${data.wisdom} (${Math.floor((data.wisdom - 10) / 2)})</p></div>
                <div><strong class="title-font">CHA</strong><p>${data.charisma} (${Math.floor((data.charisma - 10) / 2)})</p></div>
            </div>`;
        const proficiencies = formatArrayField(data.proficiencies, p => `${escapeHTML(p.proficiency.name)} +${p.value}`);
        const conditionImmunities = formatArrayField(data.condition_immunities, c => escapeHTML(c.name));
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
            <hr class="border-border-color my-2">
            ${abilityScores}
            <hr class="border-border-color my-2">
            <div class="text-sm space-y-1">
                <p><strong class="title-font">Proficiencies:</strong> ${proficiencies}</p>
                <p><strong class="title-font">Damage Vulnerabilities:</strong> ${Array.isArray(data.damage_vulnerabilities) ? data.damage_vulnerabilities.join(', ') : 'None'}</p>
                <p><strong class="title-font">Damage Resistances:</strong> ${Array.isArray(data.damage_resistances) ? data.damage_resistances.join(', ') : 'None'}</p>
                <p><strong class="title-font">Damage Immunities:</strong> ${Array.isArray(data.damage_immunities) ? data.damage_immunities.join(', ') : 'None'}</p>
                <p><strong class="title-font">Condition Immunities:</strong> ${conditionImmunities}</p>
                <p><strong class="title-font">Senses:</strong> ${senses}</p>
                <p><strong class="title-font">Languages:</strong> ${escapeHTML(data.languages)}</p>
                <p><strong class="title-font">Challenge:</strong> ${data.challenge_rating} (${data.xp} XP)</p>
            </div>
            ${renderActionSection('Special Abilities', data.special_abilities)}
            ${renderActionSection('Actions', data.actions)}
            ${renderActionSection('Legendary Actions', data.legendary_actions)}
        `;
    };
    const renderEquipmentCard = (data) => {
        let cost = data.cost ? `${data.cost.quantity} ${data.cost.unit}` : 'N/A';
        let properties = formatArrayField(data.properties, p => p.name);
        let damage = data.damage ? `${data.damage.damage_dice} ${data.damage.damage_type.name}`: null;
        let armor_class = data.armor_class ? `Base ${data.armor_class.base}` + (data.armor_class.dex_bonus ? ' + Dex bonus' : '') + (data.armor_class.max_bonus ? ` (max ${data.armor_class.max_bonus})` : '') : null;
        return `
            <p class="text-muted">${escapeHTML(data.equipment_category?.name)}</p>
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
    };
    const renderMagicItemCard = (data) => {
         return `
            <p class="text-muted">${escapeHTML(data.equipment_category?.name)} (${escapeHTML(data.rarity)})</p>
             ${renderDescription(data.description)}
        `;
    };
    const renderClassCard = (data) => {
        const proficiencies = formatArrayField(data.proficiencies, p => p.name);
        const savingThrows = formatArrayField(data.saving_throws, st => st.name);
        return `
            <p class="text-muted">Class Features</p>
             <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 mt-4">
                ${renderDetailRow('Hit Die', `d${data.hit_die}`)}
                ${renderDetailRow('Saving Throws', savingThrows)}
             </div>
             <hr class="border-border-color my-4">
             <p><strong class="title-font">Proficiencies:</strong> ${proficiencies}</p>
             ${data.spellcasting ? `<p class="mt-2"><strong class="title-font">Spellcasting Ability:</strong> ${escapeHTML(data.spellcasting.spellcasting_ability.name)}</p>` : ''}
        `;
    };
    const renderRaceCard = (data) => {
        const abilityBonuses = formatArrayField(data.ability_bonuses, b => `${b.ability_score.name} +${b.bonus}`);
        const languages = formatArrayField(data.languages, l => l.name);
        return `
            <p class="text-muted">${escapeHTML(data.size)} Humanoid</p>
             <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 mt-4">
                ${renderDetailRow('Speed', `${data.speed} ft.`)}
                ${renderDetailRow('Ability Bonuses', abilityBonuses)}
             </div>
             <hr class="border-border-color my-4">
             <p><strong class="title-font">Languages:</strong> ${languages}</p>
             ${renderDescription(data.alignment)}
             ${renderDescription(data.age)}
             ${renderDescription(data.size_description)}
        `;
    };
    const renderSubclassCard = (data) => {
        const spells = formatArrayField(data.spells, s => `${s.spell.name} (Level ${s.prerequisites.find(p => p.type === 'level')?.level})`);
        return `
            <p class="text-muted">Subclass for ${escapeHTML(data.class?.name)}</p>
            ${renderDescription(data.subclass_flavor)}
            ${renderDescription(data.description)}
            <hr class="border-border-color my-4">
            <p><strong class="title-font">Subclass Spells:</strong> ${spells}</p>
        `;
    };
    const renderSubraceCard = (data) => {
        const abilityBonuses = formatArrayField(data.ability_bonuses, b => `${b.ability_score.name} +${b.bonus}`);
        const languages = formatArrayField(data.languages, l => l.name);
        const startingProficiencies = formatArrayField(data.starting_proficiencies, p => p.name);
        return `
            <p class="text-muted">Subrace of ${escapeHTML(data.race?.name)}</p>
            ${renderDescription(data.description)}
            <hr class="border-border-color my-4">
            <div class="space-y-2">
                <p><strong class="title-font">Ability Bonuses:</strong> ${abilityBonuses}</p>
                <p><strong class="title-font">Starting Proficiencies:</strong> ${startingProficiencies}</p>
                <p><strong class="title-font">Languages:</strong> ${languages}</p>
            </div>
        `;
    };
    const renderSkillCard = (data) => {
        return `
            <p class="text-muted">Ability Score: ${escapeHTML(data.ability_score?.name)}</p>
            ${renderDescription(data.description)}
        `;
    };
    const renderTraitCard = (data) => {
        const races = formatArrayField(data.races, r => r.name);
        const subraces = formatArrayField(data.subraces, s => s.name);
        return `
            <p class="text-muted">Racial Trait</p>
            ${renderDescription(data.description)}
            <hr class="border-border-color my-4">
            <p><strong class="title-font">Races:</strong> ${races}</p>
            <p><strong class="title-font">Subraces:</strong> ${subraces}</p>
        `;
    };
    const renderFeatureCard = (data) => {
        return `
            <p class="text-muted">${escapeHTML(data.class?.name)} Feature (Level ${data.level})</p>
            ${data.subclass ? `<p class="text-muted">${escapeHTML(data.subclass?.name)} Subclass</p>` : ''}
            ${renderDescription(data.description)}
        `;
    };
    const renderConditionCard = (data) => {
        return `
            <p class="text-muted">Game Condition</p>
            ${renderDescription(data.description)}
        `;
    };
    const renderAbilityScoreCard = (data) => {
        const skills = formatArrayField(data.skills, s => s.name);
        return `
            <p class="text-muted">Core Attribute</p>
            ${renderDescription(data.description)}
            <hr class="border-border-color my-4">
            <p><strong class="title-font">Associated Skills:</strong> ${skills}</p>
        `;
    };
    const renderProficiencyCard = (data) => {
        return `
            <p class="text-muted">Proficiency Type: ${escapeHTML(data.type)}</p>
            <hr class="border-border-color my-4">
            ${renderListSection('Granted by Classes', data.classes)}
            ${renderListSection('Granted by Races', data.races)}
        `;
    };
    const renderLanguageCard = (data) => {
        const speakers = Array.isArray(data.typical_speakers) ? data.typical_speakers.join(', ') : data.typical_speakers;
        return `
             <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 mt-4">
                ${renderDetailRow('Type', data.type)}
                ${renderDetailRow('Script', data.script)}
             </div>
             <hr class="border-border-color my-4">
             <p><strong class="title-font">Typical Speakers:</strong> ${escapeHTML(speakers) || 'None'}</p>
        `;
    };
    const renderEquipmentCategoryCard = (data) => {
        return `
            <p class="text-muted">Category of Equipment</p>
            <hr class="border-border-color my-4">
            ${renderListSection('Items in this category', data.equipment)}
        `;
    };
    const renderDamageTypeCard = (data) => {
        return `
            <p class="text-muted">Type of Damage</p>
            ${renderDescription(data.description)}
        `;
    };
    const renderGenericCard = (data) => {
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
    };

    const renderers = {
        spells: renderSpellCard,
        monsters: renderMonsterCard,
        equipment: renderEquipmentCard,
        magic_items: renderMagicItemCard,
        classes: renderClassCard,
        races: renderRaceCard,
        subclasses: renderSubclassCard,
        subraces: renderSubraceCard,
        skills: renderSkillCard,
        traits: renderTraitCard,
        features: renderFeatureCard,
        conditions: renderConditionCard,
        ability_scores: renderAbilityScoreCard,
        proficiencies: renderProficiencyCard,
        languages: renderLanguageCard,
        equipment_categories: renderEquipmentCategoryCard,
        damage_types: renderDamageTypeCard,
    };

    const displayDetail = (data) => {
        const renderer = renderers[currentCategory] || renderGenericCard;
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
                    displayDetail(itemData);
                } catch(e) {
                    console.error("Failed to parse item data:", itemButton.getAttribute('data-item'), e);
                    resultsContainer.innerHTML = `<p class="text-red-500 text-center font-semibold p-8">Could not load item details. Data might be corrupted.</p>`;
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
                categoryNav.querySelector('.active-sheet-tab').classList.remove('active-sheet-tab');
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
