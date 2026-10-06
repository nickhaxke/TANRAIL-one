@extends('layouts.management')

@section('content')
<div class="space-y-6" x-data="{
    name: '{{ old('name') }}',
    code: '{{ old('code', 'BU-REST') }}',
    category: '{{ old('category', 'Restaurant & Food Services') }}',
    description: '{{ old('description') }}',
    costCenter: '{{ old('cost_center', 'CC-4100-REST') }}',
    managerUserId: '{{ old('manager_user_id') }}',
    managerName: '{{ old('manager_name') }}',
    managerEmail: '{{ old('manager_email') }}',
    managerPhone: '{{ old('manager_phone') }}',
    status: {{ old('status', true) ? 'true' : 'false' }},
    users: {{ Js::from($users->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'email' => $u->email])) }},
    onCategoryChange() {
        const presets = {
            'Restaurant & Food Services': { code: 'BU-REST', costCenter: 'CC-4100-REST' },
            'On-board Train Catering': { code: 'BU-CAT', costCenter: 'CC-4200-CAT' },
            'Station Kiosks & Retail': { code: 'BU-RETL', costCenter: 'CC-4300-RETL' },
            'Commercial Services & Facilities': { code: 'BU-COMM', costCenter: 'CC-4400-COMM' },
            'Facilities Management': { code: 'BU-FAC', costCenter: 'CC-4600-FAC' },
            'Cleaning Operations': { code: 'BU-CLN', costCenter: 'CC-4700-CLN' },
            'Logistics & Supply Chain': { code: 'BU-LOG', costCenter: 'CC-4500-LOG' }
        };
        if (presets[this.category]) {
            if (!this.code || this.code.startsWith('BU-')) {
                this.code = presets[this.category].code;
            }
            if (!this.costCenter || this.costCenter.startsWith('CC-')) {
                this.costCenter = presets[this.category].costCenter;
            }
        }
    },
    onManagerChange() {
        if (!this.managerUserId) {
            return;
        }
        const user = this.users.find(u => u.id == this.managerUserId);
        if (user) {
            this.managerName = user.name;
            this.managerEmail = user.email;
        }
    }
}">
    <!-- Header Section -->
    <div class="bg-white border border-gray-300 rounded-md p-5 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-x-2 text-xs font-medium text-gray-500 mb-1">
                    <a href="{{ route('management.organization.business-units') }}" class="hover:text-blue-600 transition-colors">Business Units Hub</a>
                    <span>/</span>
                    <span>New Registration</span>
                </div>
                <h1 class="text-xl font-bold text-gray-900">Register Commercial Division</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Establish a new autonomous business unit under {{ $organization->name ?? 'TANRAIL Investments Limited' }}.
                </p>
            </div>
            <div>
                <a href="{{ route('management.organization.business-units') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm border border-gray-300 hover:bg-gray-50 transition-colors">
                    &larr; Back to Directory
                </a>
            </div>
        </div>
    </div>

    <!-- Form & Preview Grid -->
    <form action="{{ route('management.business-units.store-direct') }}" method="POST" autocomplete="off" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        @csrf
        <input type="hidden" name="organization_id" value="{{ $organization->id ?? '' }}">

        <!-- Left Column: Form Fields -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Section 1: Core Division Identity -->
            <div class="bg-white border border-gray-300 rounded-md p-6 shadow-sm space-y-5">
                <div class="flex items-center gap-2 pb-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-900">Division Identity & Classification</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Name -->
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-xs font-bold uppercase text-gray-700 mb-1">
                            Business Unit Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" x-model="name" autocomplete="off" placeholder="Enter division name" class="block w-full rounded border border-gray-300 py-2 px-3 text-sm text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                        <p class="mt-1 text-[11px] text-gray-500">Official trade name printed on commercial invoices.</p>
                        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Category / Sector -->
                    <div>
                        <label for="category" class="block text-xs font-bold uppercase text-gray-700 mb-1">
                            Operational Sector
                        </label>
                        <select name="category" id="category" x-model="category" @change="onCategoryChange()" class="block w-full rounded border border-gray-300 py-2 px-3 text-sm text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="Restaurant & Food Services">Restaurant & Food Services</option>
                            <option value="On-board Train Catering">On-board Train Catering</option>
                            <option value="Station Kiosks & Retail">Station Kiosks & Retail</option>
                            <option value="Commercial Services & Facilities">Commercial Services & Facilities</option>
                            <option value="Facilities Management">Facilities Management</option>
                            <option value="Cleaning Operations">Cleaning Operations</option>
                            <option value="Logistics & Supply Chain">Logistics & Supply Chain</option>
                        </select>
                        <p class="mt-1 text-[11px] text-gray-500">Auto-suggests code presets.</p>
                    </div>

                    <!-- Code -->
                    <div>
                        <label for="code" class="block text-xs font-bold uppercase text-gray-700 mb-1">
                            Division Code <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="code" id="code" x-model="code" autocomplete="off" placeholder="BU-REST" class="block w-full rounded border border-gray-300 py-2 px-3 text-sm uppercase text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                        <p class="mt-1 text-[11px] text-gray-500">Unique prefix identifier.</p>
                        @error('code')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Description -->
                    <div class="sm:col-span-2">
                        <label for="description" class="block text-xs font-bold uppercase text-gray-700 mb-1">
                            Operational Mandate
                        </label>
                        <textarea name="description" id="description" x-model="description" rows="3" class="block w-full rounded border border-gray-300 py-2 px-3 text-sm text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
                    </div>
                </div>
            </div>

            <!-- Section 2: Administrative Governance & Cost Center -->
            <div class="bg-white border border-gray-300 rounded-md p-6 shadow-sm space-y-5">
                <div class="flex items-center gap-2 pb-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-900">Governance Leadership & Cost Allocation</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Assign Manager -->
                    <div class="sm:col-span-2">
                        <label for="manager_user_id" class="block text-xs font-bold uppercase text-gray-700 mb-1">
                            Assign Unit Head (Registered User)
                        </label>
                        <select name="manager_user_id" id="manager_user_id" x-model="managerUserId" @change="onManagerChange()" class="block w-full rounded border border-gray-300 py-2 px-3 text-sm text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="">-- Manual / External Manager Nomination --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('manager_user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} &bull; {{ $user->email }}
                                </option>
                            @endforeach
                        </select>
                        @error('manager_user_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Manager Name -->
                    <div>
                        <label for="manager_name" class="block text-xs font-bold uppercase text-gray-700 mb-1">
                            Unit Head Display Name
                        </label>
                        <input type="text" name="manager_name" id="manager_name" x-model="managerName" autocomplete="off" class="block w-full rounded border border-gray-300 py-2 px-3 text-sm text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>

                    <!-- Cost Center -->
                    <div>
                        <label for="cost_center" class="block text-xs font-bold uppercase text-gray-700 mb-1">
                            Financial Cost Center
                        </label>
                        <input type="text" name="cost_center" id="cost_center" x-model="costCenter" autocomplete="off" class="block w-full rounded border border-gray-300 py-2 px-3 text-sm uppercase text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>

                    <!-- Manager Email -->
                    <div>
                        <label for="manager_email" class="block text-xs font-bold uppercase text-gray-700 mb-1">
                            Contact Email Address
                        </label>
                        <input type="email" name="manager_email" id="manager_email" x-model="managerEmail" autocomplete="off" class="block w-full rounded border border-gray-300 py-2 px-3 text-sm text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>

                    <!-- Manager Phone -->
                    <div>
                        <label for="manager_phone" class="block text-xs font-bold uppercase text-gray-700 mb-1">
                            Contact Phone Number
                        </label>
                        <input type="text" name="manager_phone" id="manager_phone" x-model="managerPhone" autocomplete="off" class="block w-full rounded border border-gray-300 py-2 px-3 text-sm text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Section 3: Operational Status -->
            <div class="bg-white border border-gray-300 rounded-md p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <span class="text-sm font-semibold text-gray-900 block">Active Operational Status</span>
                        <p class="text-[11px] text-gray-500">
                            When enabled, this division is immediately active.
                        </p>
                    </div>
                    <!-- AlpineJS Toggle -->
                    <button type="button" 
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                            :class="status ? 'bg-blue-600' : 'bg-gray-200'"
                            @click="status = !status">
                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                              :class="status ? 'translate-x-5' : 'translate-x-0'"></span>
                    </button>
                    <!-- Hidden input for form submission -->
                    <input type="hidden" name="status" :value="status ? '1' : '0'">
                </div>
            </div>

            <!-- Action Bar -->
            <div class="flex items-center justify-end gap-x-3 pt-2">
                <a href="{{ route('management.organization.business-units') }}" class="rounded-md px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center rounded-md bg-blue-600 px-5 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors border border-blue-600">
                    Save Business Unit
                </button>
            </div>
        </div>

        <!-- Right Column: Preview -->
        <div class="lg:col-span-4 space-y-6">
            <div class="sticky top-6 space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase text-gray-500">Live Card Preview</span>
                </div>

                <!-- Division Card Mockup (Matching Index Style) -->
                <div class="bg-white rounded-md border border-gray-300 p-5 shadow-sm flex flex-col relative">
                    <div class="absolute -top-6 left-1/2 w-px h-6 bg-gray-300"></div>

                    <div class="flex items-start justify-between gap-2 mb-4">
                        <div>
                            <span class="text-xs font-medium text-gray-600 bg-gray-100 px-1.5 py-0.5 rounded border border-gray-200" x-text="code ? code.toUpperCase() : 'CODE'">
                            </span>
                            <h3 class="text-base font-bold text-gray-900 mt-1" x-text="name ? name : 'New Division Title'"></h3>
                            <p class="text-xs text-gray-500 mt-0.5" x-text="category"></p>
                        </div>
                        
                        <!-- Status Indicator -->
                        <span x-show="status" class="inline-flex items-center rounded bg-green-100 px-2 py-0.5 text-[11px] font-medium text-green-800">
                            Active
                        </span>
                        <span x-show="!status" class="inline-flex items-center rounded bg-gray-100 px-2 py-0.5 text-[11px] font-medium text-gray-800">
                            Inactive
                        </span>
                    </div>

                    <div class="p-3 rounded bg-gray-50 border border-gray-200 mb-4 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <div class="h-6 w-6 rounded bg-gray-200 text-gray-600 font-bold text-xs flex items-center justify-center shrink-0">
                                <span x-text="managerName ? managerName.substring(0, 1) : 'M'"></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold uppercase text-gray-500 block">Unit Manager</span>
                                <p class="text-xs font-medium text-gray-900" x-text="managerName ? managerName : 'Unassigned'"></p>
                            </div>
                        </div>
                        <span class="text-[10px] font-medium text-gray-600" x-text="costCenter"></span>
                    </div>

                    <div class="text-[11px] font-bold text-gray-500 border-t border-gray-200 pt-3">
                        LEVEL 3: Outlets (0)
                    </div>
                </div>

                <!-- Governance Guidance Card -->
                <div class="bg-gray-50 rounded-md p-4 border border-gray-200">
                    <div class="flex items-center gap-2 text-gray-900 font-semibold text-xs mb-2">
                        <span>Governance Rules</span>
                    </div>
                    <ul class="text-[11px] text-gray-600 space-y-1.5 list-disc list-inside">
                        <li>Unit operates with an isolated ledger prefix.</li>
                        <li>Branches are mapped directly to this unit.</li>
                        <li>Staff roles scoped to this division.</li>
                    </ul>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
