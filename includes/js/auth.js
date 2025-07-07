/*
    Dack's DND Tools - includes/js/auth.js
    ======================================
    This file handles all client-side logic for user authentication forms,
    including registration, login, and profile page interactions.
*/

// --- SECTION 1: UTILITY FUNCTIONS ---
// ====================================
// Reusable helper functions used across different forms.

/**
 * Displays a message (error or success) to the user in a designated message area.
 * @param {HTMLElement} messageArea - The DOM element to display the message in.
 * @param {string} message - The message text to display.
 * @param {boolean} isError - If true, styles the message as an error.
 */
const showMessage = (messageArea, message, isError = true) => {
    if (!messageArea) return;
    messageArea.textContent = message;
    // Reset classes and apply styles based on message type.
    messageArea.className = 'p-4 mb-6 rounded-md text-center';
    if (isError) {
        messageArea.classList.add('bg-red-900', 'text-white');
    } else {
        messageArea.classList.add('bg-green-900', 'text-white');
    }
    messageArea.classList.remove('hidden');
};


// --- SECTION 2: REGISTRATION PAGE LOGIC ---
// ==========================================
// Manages the user registration form and its CAPTCHA.

const initRegistration = () => {
    const regPage = document.getElementById('registration-page');
    if (!regPage) return; // Exit if not on the registration page.

    const form = document.getElementById('register-form');
    const messageArea = form.querySelector('#form-message');
    const captchaQuestionEl = form.querySelector('#captcha-question');
    const captchaInput = form.querySelector('#captcha');
    
    let captchaAnswer = 0;

    // Generates a new random math question for the CAPTCHA.
    const generateCaptcha = () => {
        const num1 = Math.floor(Math.random() * 10) + 1;
        const num2 = Math.floor(Math.random() * 10) + 1;
        captchaAnswer = num1 + num2;
        if(captchaQuestionEl) captchaQuestionEl.textContent = `What is ${num1} + ${num2}?`;
    };

    // Handles the registration form submission.
    const handleRegisterSubmit = async (e) => {
        e.preventDefault();
        
        // Client-side validation before sending to the API.
        if (form.password.value !== form.confirmPassword.value) {
            showMessage(messageArea, "Passwords do not match.");
            return;
        }
        if (parseInt(captchaInput.value, 10) !== captchaAnswer) {
            showMessage(messageArea, "Incorrect answer to the security question.");
            generateCaptcha();
            return;
        }

        const formData = {
            firstName: form.firstName.value,
            lastName: form.lastName.value,
            displayName: form.displayName.value,
            email: form.email.value,
            password: form.password.value
        };

        showMessage(messageArea, "Processing...", false);

        try {
            const response = await fetch('includes/user/register.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData),
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.error || 'An unknown error occurred.');
            
            form.reset();
            generateCaptcha();
            showMessage(messageArea, "Registration successful! You can now log in.", false);
        } catch (error) {
            showMessage(messageArea, error.message);
            generateCaptcha();
        }
    };

    if(form) form.addEventListener('submit', handleRegisterSubmit);
    generateCaptcha(); // Generate the first CAPTCHA on page load.
};


// --- SECTION 3: LOGIN PAGE LOGIC ---
// ===================================
// Manages the user login form.

const initLogin = () => {
    const loginPage = document.getElementById('login-page');
    if (!loginPage) return; // Exit if not on the login page.

    const form = document.getElementById('login-form');
    const messageArea = form.querySelector('#form-message');

    // Handles the login form submission.
    const handleLoginSubmit = async (e) => {
        e.preventDefault();
        const formData = {
            email: form.email.value,
            password: form.password.value,
        };
        showMessage(messageArea, "Logging in...", false);
        try {
            const response = await fetch('includes/user/login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData),
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.error || 'An unknown error occurred.');
            // On successful login, redirect to the home page.
            window.location.href = 'home';
        } catch (error) {
            showMessage(messageArea, error.message);
        }
    };

    if(form) form.addEventListener('submit', handleLoginSubmit);
};


// --- SECTION 4: PROFILE PAGE LOGIC ---
// =====================================
// Manages all interactivity on the user profile page.

const initProfile = () => {
    const profilePage = document.getElementById('profile-page');
    if (!profilePage) return; // Exit if not on the profile page.

    // Cache all elements needed for the profile page.
    const tabs = {
        view: { btn: document.getElementById('view-tab'), panel: document.getElementById('view-panel') },
        edit: { btn: document.getElementById('edit-tab'), panel: document.getElementById('edit-panel') },
        password: { btn: document.getElementById('password-tab'), panel: document.getElementById('password-panel') }
    };
    const editForm = document.getElementById('edit-profile-form');
    const passwordForm = document.getElementById('change-password-form');
    const messageArea = document.getElementById('form-message');

    // Handles switching between the View, Edit, and Password tabs.
    const switchTab = (targetTab) => {
        messageArea.classList.add('hidden'); // Hide any previous messages.
        for (const key in tabs) {
            if (tabs[key].btn && tabs[key].panel) {
                const isTarget = key === targetTab;
                tabs[key].btn.classList.toggle('active-sheet-tab', isTarget);
                tabs[key].panel.classList.toggle('hidden', !isTarget);
            }
        }
    };

    // Attach event listeners for tab buttons.
    Object.keys(tabs).forEach(key => {
        if (tabs[key].btn) {
            tabs[key].btn.addEventListener('click', () => switchTab(key));
        }
    });
    
    // Handles submission of the "Edit Profile" form.
    if (editForm) {
        editForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = {
                firstName: editForm.firstName.value,
                lastName: editForm.lastName.value,
                displayName: editForm.displayName.value,
            };
            showMessage(messageArea, 'Saving...', false);
            try {
                const response = await fetch('includes/user/update_profile.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(formData),
                });
                const result = await response.json();
                if (!response.ok) throw new Error(result.error || 'An unknown error occurred.');
                
                showMessage(messageArea, result.success, false);
                // Update the static profile information in the "View" tab.
                profilePage.querySelector('.profile-display-name').textContent = formData.displayName;
                profilePage.querySelector('.profile-full-name').textContent = `${formData.firstName} ${formData.lastName}`;
                const pageTitle = document.querySelector('#profile-page h1');
                if (pageTitle) pageTitle.textContent = `${formData.displayName}'s Profile`;
                // Switch back to the view tab after a successful update.
                switchTab('view');
            } catch (error) {
                showMessage(messageArea, error.message, true);
            }
        });
    }
    
    // Handles submission of the "Change Password" form.
    if (passwordForm) {
        passwordForm.addEventListener('submit', async(e) => {
            e.preventDefault();
            const currentPassword = passwordForm.currentPassword.value;
            const newPassword = passwordForm.newPassword.value;
            const confirmPassword = passwordForm.confirmPassword.value;

            if (newPassword !== confirmPassword) {
                showMessage(messageArea, "New passwords do not match.", true);
                return;
            }

            const formData = { currentPassword, newPassword, confirmPassword };
            showMessage(messageArea, 'Updating password...', false);
            
            try {
                const response = await fetch('includes/user/change_password.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(formData),
                });
                const result = await response.json();
                if(!response.ok) throw new Error(result.error || 'An unknown error occurred.');
                
                showMessage(messageArea, result.success, false);
                passwordForm.reset();
                // Switch back to the view tab after a successful update.
                switchTab('view');
            } catch (error) {
                 showMessage(messageArea, error.message, true);
            }
        });
    }
};

// --- SECTION 5: DOMContentLoaded EVENT ---
// =======================================
// This is the main execution block. It runs after the entire page has loaded
// and calls the initialization functions for any relevant pages.
document.addEventListener('DOMContentLoaded', () => {
    initRegistration();
    initLogin();
    initProfile();
});
