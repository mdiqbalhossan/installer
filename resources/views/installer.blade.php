<!DOCTYPE html>
<html lang="en" x-data="installer()">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Softmax Installer Wizard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpine-collective/toolkit@1.2.0/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .step-transition {
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        .step-hidden {
            opacity: 0;
            transform: translateX(20px);
            display: none;
        }
        .step-visible {
            opacity: 1;
            transform: translateX(0);
            display: block;
        }
        .progress-bar {
            transition: width 0.5s ease;
        }
        .brand-color {
            color: #FF0066;
        }
        .brand-bg {
            background-color: #FF0066;
        }
        .brand-border {
            border-color: #FF0066;
        }
        .btn-primary {
            background-color: #FF0066;
        }
        .btn-primary:hover {
            background-color: #e0005a;
        }
        .btn-primary:disabled {
            background-color: #fca5a5;
            cursor: not-allowed;
        }
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col items-center justify-center p-4" x-cloak x-data="installer()">
    <!-- Toast Container -->
    <div class="fixed top-4 right-4 z-50 space-y-3" style="z-index: 10000; max-width: 400px; width: auto;">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="true" 
                 x-transition:enter="transform transition ease-out duration-300"
                 x-transition:enter-start="translate-x-full opacity-0 scale-95"
                 x-transition:enter-end="translate-x-0 opacity-100 scale-100"
                 x-transition:leave="transform transition ease-in duration-200"
                 x-transition:leave-start="translate-x-0 opacity-100 scale-100"
                 x-transition:leave-end="translate-x-full opacity-0 scale-95"
                 class="min-w-80 w-auto shadow-xl rounded-lg pointer-events-auto border-l-4"
                 :class="toast.type === 'success' ? 'bg-white border-l-green-500 border border-green-100' : 'bg-white border-l-red-500 border border-red-100'">
                <div class="p-4">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 mt-0.5">
                            <i :class="toast.type === 'success' ? 'fas fa-check-circle text-green-500' : 'fas fa-exclamation-circle text-red-500'" class="text-xl"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold" :class="toast.type === 'success' ? 'text-green-800' : 'text-red-800'" x-text="toast.title"></p>
                            <p class="mt-1 text-sm leading-relaxed" :class="toast.type === 'success' ? 'text-green-700' : 'text-red-700'" x-text="toast.message"></p>
                        </div>
                        <div class="flex-shrink-0">
                            <button @click="toasts.splice(toasts.indexOf(toast), 1)"
                                    class="rounded-md p-1 text-gray-400 hover:text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                                <span class="sr-only">Close</span>
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
    <!-- Logo outside card -->
    <div class="mb-6 flex justify-center">
        <div class="flex items-center">
            <div class="w-10 h-10 rounded-lg brand-bg flex items-center justify-center text-white mr-3">
                <i class="fas fa-cube text-xl"></i>
            </div>
            <span class="text-2xl font-bold brand-color">Softmax</span>
        </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl overflow-hidden mb-8">
        <!-- Progress Bar -->
        <div class="px-6 pt-6">
            <div class="hidden md:block w-full bg-gray-200 rounded-full h-2.5 mb-6">
                <div class="progress-bar brand-bg h-2.5 rounded-full" :style="'width: ' + (currentStep / 5 * 100) + '%'"></div>
            </div>
            <div class="flex justify-between mb-6 md:hidden">
                <div class="flex items-center" :class="{'brand-color': currentStep >= 1}">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center" 
                         :class="currentStep >= 1 ? 'brand-bg text-white' : 'bg-gray-200 text-gray-500'">1</div>
                    <div class="text-xs ml-2 font-medium">Requirements</div>
                </div>
                <div class="flex items-center" :class="{'brand-color': currentStep >= 2}">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center" 
                         :class="currentStep >= 2 ? 'brand-bg text-white' : 'bg-gray-200 text-gray-500'">2</div>
                    <div class="text-xs ml-2 font-medium">Permissions</div>
                </div>
                <div class="flex items-center" :class="{'brand-color': currentStep >= 3}">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center" 
                         :class="currentStep >= 3 ? 'brand-bg text-white' : 'bg-gray-200 text-gray-500'">3</div>
                    <div class="text-xs ml-2 font-medium">License</div>
                </div>
                <div class="flex items-center" :class="{'brand-color': currentStep >= 4}">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center" 
                         :class="currentStep >= 4 ? 'brand-bg text-white' : 'bg-gray-200 text-gray-500'">4</div>
                    <div class="text-xs ml-2 font-medium">Database</div>
                </div>
                <div class="flex items-center" :class="{'brand-color': currentStep >= 5}">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center" 
                         :class="currentStep >= 5 ? 'brand-bg text-white' : 'bg-gray-200 text-gray-500'">5</div>
                    <div class="text-xs ml-2 font-medium">Finalize</div>
                </div>
            </div>
        </div>

        <!-- Steps Container -->
        <div class="px-6 pb-6 relative">
            <!-- Step 1: Server Requirements -->
            <div x-show="currentStep === 1" class="step-transition" x-transition:enter="step-visible" x-transition:leave="step-hidden">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 flex items-center justify-center rounded-full bg-pink-100 brand-color mr-3">
                        <i class="fas fa-server"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">Server Requirements</h2>
                        <p class="text-gray-600">Check if your server meets the software requirements</p>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg mt-6">
                    <h3 class="font-medium text-gray-700 mb-3">PHP Extensions</h3>
                    <ul class="space-y-2" x-show="systemInfo.requirements">
                        <template x-for="requirement in systemInfo.requirements" :key="requirement.name">
                            <li class="flex items-center">
                                <span class="w-5 h-5 inline-flex items-center justify-center rounded-full mr-2"
                                      :class="requirement.installed ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'">
                                    <i class="text-xs" :class="requirement.installed ? 'fas fa-check' : 'fas fa-times'"></i>
                                </span>
                                <span x-text="requirement.name.toUpperCase() + ' Extension'"></span>
                            </li>
                        </template>
                    </ul>

                    <div x-show="loading" class="text-center py-4">
                        <i class="fas fa-spinner fa-spin text-xl text-gray-500"></i>
                        <p class="text-gray-500 mt-2">Checking requirements...</p>
                    </div>
                </div>
            </div>

            <!-- Step 2: Directory Permissions -->
            <div x-show="currentStep === 2" class="step-transition" x-transition:enter="step-visible" x-transition:leave="step-hidden">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 flex items-center justify-center rounded-full bg-pink-100 brand-color mr-3">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">Directory Permissions</h2>
                        <p class="text-gray-600">Check if required directories are writable</p>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg mt-6">
                    <h3 class="font-medium text-gray-700 mb-3">Directory Permissions</h3>
                    <ul class="space-y-2" x-show="systemInfo.permissions">
                        <template x-for="permission in systemInfo.permissions" :key="permission.path">
                            <li class="flex items-center">
                                <span class="w-5 h-5 inline-flex items-center justify-center rounded-full mr-2"
                                      :class="permission.writable ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'">
                                    <i class="text-xs" :class="permission.writable ? 'fas fa-check' : 'fas fa-times'"></i>
                                </span>
                                <span x-text="'/' + permission.path + ' directory (writable)'"></span>
                            </li>
                        </template>
                    </ul>

                    <div x-show="!systemInfo.can_proceed && systemInfo.requirements" class="mt-4 p-3 bg-red-50 text-red-700 rounded-lg">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        Please fix the highlighted requirements before continuing.
                    </div>
                </div>
            </div>

            <!-- Step 3: License Validation -->
            <div x-show="currentStep === 3" class="step-transition" x-transition:enter="step-visible" x-transition:leave="step-hidden">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 flex items-center justify-center rounded-full bg-pink-100 brand-color mr-3">
                        <i class="fas fa-key"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">License Validation</h2>
                        <p class="text-gray-600">Enter your customer ID and license key</p>
                    </div>
                </div>

                <div class="mt-6 space-y-4">
                    <div>
                        <label for="customerId" class="block text-sm font-medium text-gray-700 mb-1">Customer ID</label>
                        <input type="text" id="customerId" x-model="license.customerId" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-pink-200 focus:border-pink-400 outline-none transition" placeholder="e.g. CUST-12345">
                    </div>
                    <div>
                        <label for="licenseKey" class="block text-sm font-medium text-gray-700 mb-1">License Key</label>
                        <input type="text" id="licenseKey" x-model="license.licenseKey" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-pink-200 focus:border-pink-400 outline-none transition" placeholder="e.g. XXXX-XXXX-XXXX-XXXX">
                    </div>
                    <div class="pt-2">
                        <button @click="validateLicense()" :disabled="validatingLicense" class="w-full btn-primary text-white py-2.5 px-4 rounded-lg font-medium transition flex items-center justify-center">
                            <span x-text="validatingLicense ? 'Validating...' : 'Validate License'"></span>
                            <i class="fas fa-spinner fa-spin ml-2" x-show="validatingLicense"></i>
                        </button>
                    </div>
                    <div x-show="licenseValidated" class="p-3 bg-green-50 text-green-700 rounded-lg">
                        <i class="fas fa-check-circle mr-2"></i>
                        License validated successfully!
                    </div>
                </div>
            </div>

            <!-- Step 4: Database Configuration -->
            <div x-show="currentStep === 4" class="step-transition" x-transition:enter="step-visible" x-transition:leave="step-hidden">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 flex items-center justify-center rounded-full bg-pink-100 brand-color mr-3">
                        <i class="fas fa-database"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">Database Configuration</h2>
                        <p class="text-gray-600">Enter your database connection details</p>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="dbHost" class="block text-sm font-medium text-gray-700 mb-1">Database Host</label>
                        <input type="text" id="dbHost" x-model="database.host" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-pink-200 focus:border-pink-400 outline-none transition" value="localhost">
                    </div>
                    <div>
                        <label for="dbPort" class="block text-sm font-medium text-gray-700 mb-1">Port</label>
                        <input type="number" id="dbPort" x-model="database.port" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-pink-200 focus:border-pink-400 outline-none transition" value="3306">
                    </div>
                    <div>
                        <label for="dbName" class="block text-sm font-medium text-gray-700 mb-1">Database Name</label>
                        <input type="text" id="dbName" x-model="database.name" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-pink-200 focus:border-pink-400 outline-none transition" placeholder="Enter database name">
                    </div>
                    <div>
                        <label for="dbUsername" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <input type="text" id="dbUsername" x-model="database.username" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-pink-200 focus:border-pink-400 outline-none transition" placeholder="Database username">
                    </div>
                    <div class="md:col-span-2">
                        <label for="dbPassword" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" id="dbPassword" x-model="database.password" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-pink-200 focus:border-pink-400 outline-none transition" placeholder="Database password">
                    </div>
                </div>

                <div class="mt-4">
                    <button @click="testDatabase()" :disabled="testingDatabase" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2.5 px-4 rounded-lg font-medium transition flex items-center justify-center">
                        <span x-text="testingDatabase ? 'Testing Connection...' : 'Test Database Connection'"></span>
                        <i class="fas fa-spinner fa-spin ml-2" x-show="testingDatabase"></i>
                    </button>
                </div>

                <div x-show="databaseTested" class="mt-4 p-3 bg-green-50 text-green-700 rounded-lg">
                    <i class="fas fa-check-circle mr-2"></i>
                    Database connection successful!
                </div>

                <div class="mt-4 p-3 bg-blue-50 text-blue-700 rounded-lg">
                    <i class="fas fa-info-circle mr-2"></i>
                    The installer will create the necessary tables in the specified database.
                </div>
            </div>

            <!-- Step 5: Finalization -->
            <div x-show="currentStep === 5 && !installing" class="step-transition" x-transition:enter="step-visible" x-transition:leave="step-hidden">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 flex items-center justify-center rounded-full bg-pink-100 brand-color mr-3">
                        <i class="fas fa-cog"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">Final Configuration</h2>
                        <p class="text-gray-600">Set up your software and admin account</p>
                    </div>
                </div>

                <div class="mt-6 space-y-4">
                    <div>
                        <label for="softwareName" class="block text-sm font-medium text-gray-700 mb-1">Software Name</label>
                        <input type="text" id="softwareName" x-model="admin.softwareName" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-pink-200 focus:border-pink-400 outline-none transition" placeholder="Your application name" value="SmartBill">
                    </div>
                    <div>
                        <label for="softwareUrl" class="block text-sm font-medium text-gray-700 mb-1">Software URL</label>
                        <input type="url" id="softwareUrl" x-model="admin.softwareUrl" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-pink-200 focus:border-pink-400 outline-none transition" placeholder="https://your-domain.com" :value="currentUrl">
                    </div>
                    <div>
                        <label for="adminName" class="block text-sm font-medium text-gray-700 mb-1">Admin Name</label>
                        <input type="text" id="adminName" x-model="admin.name" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-pink-200 focus:border-pink-400 outline-none transition" placeholder="Administrator">
                    </div>
                    <div>
                        <label for="adminEmail" class="block text-sm font-medium text-gray-700 mb-1">Admin Email</label>
                        <input type="email" id="adminEmail" x-model="admin.email" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-pink-200 focus:border-pink-400 outline-none transition" placeholder="admin@example.com">
                    </div>
                    <div>
                        <label for="adminPassword" class="block text-sm font-medium text-gray-700 mb-1">Admin Password</label>
                        <input type="password" id="adminPassword" x-model="admin.password" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-pink-200 focus:border-pink-400 outline-none transition" placeholder="Minimum 8 characters">
                    </div>
                    <div>
                        <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input type="password" id="confirmPassword" x-model="admin.confirmPassword" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-pink-200 focus:border-pink-400 outline-none transition" placeholder="Retype your password">
                    </div>
                </div>
            </div>

            <!-- Success Screen -->
            <div x-show="currentStep === 6" class="step-transition text-center py-8" x-transition:enter="step-visible" x-transition:leave="step-hidden">
                <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-check text-2xl"></i>
                </div>
                <h2 class="text-2xl font-semibold text-gray-800 mb-2">Installation Completed</h2>
                <p class="text-gray-600 mb-6">Your SmartBill software has been successfully installed and configured.</p>
                <button @click="launchApplication()" class="btn-primary text-white py-2.5 px-6 rounded-lg font-medium transition">
                    Launch Application
                </button>
            </div>

            <!-- Installing Screen -->
            <div x-show="installing" class="step-transition text-center py-8" x-transition:enter="step-visible" x-transition:leave="step-hidden">
                <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-cog fa-spin text-2xl"></i>
                </div>
                <h2 class="text-2xl font-semibold text-gray-800 mb-2">Installing...</h2>
                <p class="text-gray-600 mb-6">Please wait while we set up your SmartBill installation.</p>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="progress-bar brand-bg h-2.5 rounded-full" style="width: 100%"></div>
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="px-6 pb-6 pt-4 bg-gray-50 flex justify-between" x-show="currentStep < 6 && !installing">
            <button @click="previousStep()" :disabled="currentStep === 1" 
                    :class="{'opacity-50 cursor-not-allowed': currentStep === 1}" 
                    class="px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 font-medium transition">
                Previous
            </button>
            <button @click="nextStep()" 
                    :disabled="!canProceed()"
                    :class="{'opacity-50 cursor-not-allowed': !canProceed()}"
                    class="px-5 py-2.5 rounded-lg btn-primary text-white font-medium transition ml-auto" 
                    x-text="getNextButtonText()">
            </button>
        </div>
    </div>

    <!-- Footer outside card -->
    <div class="text-center text-gray-600 text-sm">
        <p>© 2025 Softmax Installer Wizard v1.00. All rights reserved.</p>
        <p class="mt-1">
            <a href="#" class="brand-color hover:underline">Privacy Policy</a> • 
            <a href="#" class="brand-color hover:underline">Terms of Service</a> • 
            <a href="#" class="brand-color hover:underline">Support</a>
        </p>
    </div>

    <script>
        // Set CSRF token for AJAX requests
        window.axios = window.axios || {};
        window.axios.defaults = window.axios.defaults || {};
        window.axios.defaults.headers = window.axios.defaults.headers || {};
        window.axios.defaults.headers.common = window.axios.defaults.headers.common || {};
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const baseUrl = "{{ url('/') }}";
        document.addEventListener('alpine:init', () => {
            Alpine.data('installer', () => ({
                currentStep: 1,
                loading: true,
                validatingLicense: false,
                testingDatabase: false,
                installing: false,
                licenseValidated: false,
                databaseTested: false,
                currentUrl: baseUrl,
                toasts: [],
                
                systemInfo: {},
                license: {
                    customerId: '',
                    licenseKey: ''
                },
                database: {
                    host: 'localhost',
                    port: 3306,
                    name: '',
                    username: '',
                    password: ''
                },
                admin: {
                    softwareName: 'SmartBill',
                    softwareUrl: baseUrl,
                    name: '',
                    email: '',
                    password: '',
                    confirmPassword: ''
                },
                
                async init() {
                    await this.getSystemInfo();
                },
                
                async getSystemInfo() {
                    this.loading = true;
                    try {
                        const response = await fetch(`${baseUrl}/softmax-installer/system-info`);
                        const data = await response.json();
                        
                        if (data.success) {
                            this.systemInfo = data;
                        } else {
                            this.showError('Failed to check system requirements');
                        }
                    } catch (error) {
                        this.showError('Failed to check system requirements');
                    } finally {
                        this.loading = false;
                    }
                },
                
                async validateLicense() {
                    if (!this.license.customerId || !this.license.licenseKey) {
                        this.showError('Please enter both Customer ID and License Key');
                        return;
                    }
                    
                    this.validatingLicense = true;
                    try {
                        const response = await fetch(`${baseUrl}/softmax-installer/validate-license`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(this.license)
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            this.licenseValidated = true;
                            this.showSuccess(data.message || 'License validated successfully!');
                        } else {
                            this.showError(data.message || 'License validation failed');
                        }
                    } catch (error) {
                        this.showError('License validation failed');
                    } finally {
                        this.validatingLicense = false;
                    }
                },
                
                async testDatabase() {
                    if (!this.database.host || !this.database.name || !this.database.username) {
                        this.showError('Please fill in all required database fields');
                        return;
                    }
                    
                    this.testingDatabase = true;
                    try {
                        const response = await fetch(`${baseUrl}/softmax-installer/test-database`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(this.database)
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            this.databaseTested = true;
                            this.showSuccess(data.message || 'Database connection successful!');
                        } else {
                            this.showError(data.message || 'Database connection failed');
                        }
                    } catch (error) {
                        this.showError('Database test failed');
                    } finally {
                        this.testingDatabase = false;
                    }
                },
                
                async install() {
                    if (!this.validateFinalStep()) {
                        return;
                    }
                    
                    this.installing = true;
                    
                    try {
                        const installData = {
                            customer_id: this.license.customerId,
                            license_key: this.license.licenseKey,
                            db_host: this.database.host,
                            db_port: this.database.port,
                            db_name: this.database.name,
                            db_username: this.database.username,
                            db_password: this.database.password,
                            software_name: this.admin.softwareName,
                            software_url: this.admin.softwareUrl,
                            admin_name: this.admin.name,
                            admin_email: this.admin.email,
                            admin_password: this.admin.password
                        };
                        
                        const response = await fetch(`${baseUrl}/softmax-installer/install`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(installData)
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            this.currentStep = 6; // Success step
                            this.admin.softwareUrl = data.redirect_url;
                        } else {
                            this.showError(data.message);
                        }
                    } catch (error) {
                        this.showError('Installation failed');
                    } finally {
                        this.installing = false;
                    }
                },
                
                nextStep() {
                    if (!this.canProceed()) {
                        return;
                    }
                    
                    if (this.currentStep === 5) {
                        // Validate before installing
                        if (!this.validateFinalStep()) {
                            return;
                        }
                        this.install();
                    } else {
                        this.currentStep++;
                    }
                },
                
                previousStep() {
                    if (this.currentStep > 1) {
                        this.currentStep--;
                    }
                },
                
                canProceed() {
                    switch (this.currentStep) {
                        case 1:
                            return this.systemInfo.can_proceed;
                        case 2:
                            return this.systemInfo.can_proceed;
                        case 3:
                            return this.licenseValidated;
                        case 4:
                            return this.databaseTested;
                        case 5:
                            return true; // Always allow proceeding to final step
                        default:
                            return true;
                    }
                },
                
                validateFinalStep() {
                    if (!this.admin.softwareName || !this.admin.softwareUrl || !this.admin.name || 
                        !this.admin.email || !this.admin.password || !this.admin.confirmPassword) {
                        this.showError('Please fill all fields');
                        return false;
                    }
                    
                    if (this.admin.password !== this.admin.confirmPassword) {
                        this.showError('Passwords do not match');
                        return false;
                    }
                    
                    if (this.admin.password.length < 8) {
                        this.showError('Password must be at least 8 characters');
                        return false;
                    }
                    
                    return true;
                },
                
                getNextButtonText() {
                    switch (this.currentStep) {
                        case 5:
                            return 'Install';
                        default:
                            return 'Next';
                    }
                },
                
                launchApplication() {
                    window.location.href = this.admin.softwareUrl;
                },
                
                showError(message) {
                    this.showToast('Error', message, 'error');
                },
                
                showSuccess(message) {
                    this.showToast('Success', message, 'success');
                },
                
                showToast(title, message, type) {
                    const toast = {
                        id: Date.now(),
                        title: title,
                        message: message,
                        type: type
                    };
                    
                    this.toasts.push(toast);
                    
                    // Auto-remove toast after 5 seconds
                    setTimeout(() => {
                        const index = this.toasts.findIndex(t => t.id === toast.id);
                        if (index > -1) {
                            this.toasts.splice(index, 1);
                        }
                    }, 5000);
                }
            }))
        });
    </script>
</body>
</html>
