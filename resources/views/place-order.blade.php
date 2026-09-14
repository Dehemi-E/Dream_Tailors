@extends('layouts.app')

@section('content')
<div x-data="{ 
        garmentCategory: '', specificGarment: '', fabricSource: '', fabricModalOpen: false,
        selectedShopFabric: '', selectedShopFabricImage: '', searchId: '', customerDetails: '',
        customers: {{ isset($customers) ? $customers->toJson() : '[]' }},
        imagePreview: null, isDragging: false,
        
        fabrics: [],
        newFabricName: '', newFabricImage: '', showAddFabric: false, manageMode: false,
        
        init() {
            this.fetchFabrics();
        },
        
        fetchFabrics() {
            fetch('/api/fabrics')
                .then(r => r.json())
                .then(data => {
                    this.fabrics = data.map(f => ({
                        id: f.id,
                        name: f.name,
                        image: f.image,
                        inStock: f.in_stock == 1
                    }));
                })
                .catch(e => console.error('Error loading fabrics', e));
        },
        
        handleNewFabricImage(event) { 
            const f = event.target.files[0]; 
            if(f) this.newFabricImage = URL.createObjectURL(f); 
        },

        updateFabricImage(event, fabric) {
            const f = event.target.files[0];
            if(f) {
                if(!fabric.id) {
                    alert('Error: Fabric ID is missing! Cannot update.');
                    return;
                }
                
                let formData = new FormData();
                formData.append('image', f);
                
                let originalImage = fabric.image;
                fabric.image = URL.createObjectURL(f);
                
                fetch('/api/fabrics/' + fabric.id + '/image', { 
                    method: 'POST', 
                    body: formData,
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                })
                .then(r => r.json())
                .then(data => {
                    if(data.success) {
                        fabric.image = data.image + '?t=' + new Date().getTime();
                    } else {
                        alert('Failed to save image to server.');
                        fabric.image = originalImage;
                    }
                })
                .catch(e => {
                    console.error(e);
                    alert('Connection error while saving image.');
                    fabric.image = originalImage;
                });
            }
        },

        inStockFabrics() { return this.fabrics.filter(f => f.inStock); },
        outOfStockFabrics() { return this.fabrics.filter(f => !f.inStock); },
        
        toggleStock(fabric) { 
            fabric.inStock = !fabric.inStock;
            if(fabric.id) {
                fetch('/api/fabrics/' + fabric.id + '/toggle', { 
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
            }
        },
        
        removeFabric(fabric, index) { 
            if(confirm('Are you sure you want to remove this fabric?')) {
                this.fabrics.splice(index, 1);
                if(fabric.id) {
                    fetch('/api/fabrics/' + fabric.id, { 
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });
                }
            } 
        },
        
        addFabric() { 
            if(this.newFabricName && this.$refs.newFabricFile.files[0]){
                let formData = new FormData();
                formData.append('name', this.newFabricName);
                formData.append('image', this.$refs.newFabricFile.files[0]);
                
                fetch('/api/fabrics', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                })
                .then(r => r.json())
                .then(data => {
                    if(data.success) {
                        this.fabrics.unshift({id: data.id, name: this.newFabricName, image: data.image + '?t=' + new Date().getTime(), inStock: true});
                        this.newFabricName = '';
                        this.newFabricImage = '';
                        this.showAddFabric = false;
                        if(this.$refs.newFabricFile) this.$refs.newFabricFile.value = '';
                    } else {
                        alert('Failed to add fabric.');
                    }
                })
                .catch(e => alert('Connection error while adding fabric.'));
            } else {
                alert('Please enter a name and select an image.');
            }
        },
        
        findCustomer() { 
            if(this.searchId === '') {
                this.customerDetails = '';
                this.clearMeasurements();
                return;
            } 
            let found = this.customers.find(c => c.id == this.searchId); 
            this.customerDetails = found ? found.name + ' - ' + found.mobile : 'Customer Not Found!'; 
            
            if(found) {
                fetch('/api/customers/' + this.searchId + '/measurements')
                    .then(r => r.json())
                    .then(data => {
                        const fields = ['collar', 'chest', 'shoulder', 'sleeve_length', 'full_length', 'armhole', 'upper_waist', 'lower_waist', 'hip', 'thigh', 'outseam', 'inseam', 'bottom_hem', 'knee'];
                        fields.forEach(f => {
                            let el = document.getElementById(f);
                            if(el) {
                                el.value = (data && data[f] !== null) ? data[f] : '';
                            }
                        });
                    })
                    .catch(e => console.error('Error fetching measurements', e));
            } else {
                this.clearMeasurements();
            }
        },
        clearMeasurements() {
            const fields = ['collar', 'chest', 'shoulder', 'sleeve_length', 'full_length', 'armhole', 'upper_waist', 'lower_waist', 'hip', 'thigh', 'outseam', 'inseam', 'bottom_hem', 'knee'];
            fields.forEach(f => {
                let el = document.getElementById(f);
                if(el) el.value = '';
            });
        },

        handleFileChange(event) { const f=event.target.files[0]; if(f) this.imagePreview=URL.createObjectURL(f); },
        handleDrop(event) { this.isDragging=false; const f=event.dataTransfer.files[0]; if(f){this.imagePreview=URL.createObjectURL(f);this.$refs.fileInput.files=event.dataTransfer.files;}},
        selectFabric(name,image) { this.selectedShopFabric=name; this.selectedShopFabricImage=image; this.fabricModalOpen=false; },
        focusNext(event,nextId) { if(event.target.value&&nextId) setTimeout(()=>{const el=document.getElementById(nextId);if(el)el.focus();},100); }
    }" class="max-w-5xl mx-auto">
    
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30 flex justify-between items-center">
            <div><h2 class="text-xl font-bold text-gray-800 dark:text-white">Place New Order</h2><p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Select garment category to load measurements.</p></div>
            <span class="px-4 py-2 bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 text-xs font-bold rounded-full">#ORD-NEW</span>
        </div>
        <form action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data" class="p-6 lg:p-8 space-y-8" onsubmit="document.getElementById('loading-overlay').classList.remove('hidden');">
            @csrf
            
            <!-- Customer ID & Garment Category -->
            <div class="space-y-6">
                <!-- Customer ID -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Customer ID <span class="text-red-500">*</span></label>
                    <div class="flex gap-3">
                        <div class="relative w-1/3 md:w-1/4">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 text-sm font-bold">CUST-</span>
                            <input type="text" name="customer_id" x-model="searchId" :value="searchId" @input="findCustomer()" oninput="this.value=this.value.replace(/[^0-9]/g,'')" placeholder="1" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 p-3.5 pl-16 text-sm font-bold focus:ring-2 focus:ring-primary-500 outline-none">
                        </div>
                        <input type="text" x-model="customerDetails" readonly placeholder="Name & Mobile" class="w-2/3 md:w-3/4 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 p-3.5 text-sm font-semibold outline-none cursor-not-allowed">
                    </div>
                </div>

                <!-- 🟢 Garment Category Buttons (අලුත් Icons ටික මෙතන තියෙන්නේ) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Garment Category <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <!-- Upper Body (Shirt Icon) -->
                        <label class="cursor-pointer group">
                            <input type="radio" name="garment_category" value="top" x-model="garmentCategory" required class="sr-only peer">
                            <div class="p-4 border-2 border-gray-200 dark:border-gray-600 rounded-2xl peer-checked:border-primary-500 peer-checked:bg-primary-50 dark:peer-checked:bg-primary-900/20 text-center transition-all hover:border-primary-300 dark:hover:border-primary-700 hover:shadow-md">
                                <div class="w-12 h-12 mx-auto bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-3 peer-checked:bg-white dark:peer-checked:bg-primary-800/50 peer-checked:shadow-sm group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6 text-gray-500 dark:text-gray-400 peer-checked:text-primary-600 dark:peer-checked:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 3l-4 3v4l3 1.5V21h12V11.5L21 10V6l-4-3H7z M11 3v4h2V3"/></svg>
                                </div>
                                <p class="text-sm font-bold text-gray-800 dark:text-gray-200 peer-checked:text-primary-700 dark:peer-checked:text-primary-400">Upper Body</p>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wider">Shirts, Coats</p>
                            </div>
                        </label>
                        <!-- Lower Body (Pants Icon) -->
                        <label class="cursor-pointer group">
                            <input type="radio" name="garment_category" value="bottom" x-model="garmentCategory" required class="sr-only peer">
                            <div class="p-4 border-2 border-gray-200 dark:border-gray-600 rounded-2xl peer-checked:border-primary-500 peer-checked:bg-primary-50 dark:peer-checked:bg-primary-900/20 text-center transition-all hover:border-primary-300 dark:hover:border-primary-700 hover:shadow-md">
                                <div class="w-12 h-12 mx-auto bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-3 peer-checked:bg-white dark:peer-checked:bg-primary-800/50 peer-checked:shadow-sm group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6 text-gray-500 dark:text-gray-400 peer-checked:text-primary-600 dark:peer-checked:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 3h10l1 18h-4l-2-9-2 9H6L7 3z"/></svg>
                                </div>
                                <p class="text-sm font-bold text-gray-800 dark:text-gray-200 peer-checked:text-primary-700 dark:peer-checked:text-primary-400">Lower Body</p>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wider">Trousers, Skirts</p>
                            </div>
                        </label>
                        <!-- Full Body (Shirt + Pants Icon) -->
                        <label class="cursor-pointer group">
                            <input type="radio" name="garment_category" value="full" x-model="garmentCategory" required class="sr-only peer">
                            <div class="p-4 border-2 border-gray-200 dark:border-gray-600 rounded-2xl peer-checked:border-primary-500 peer-checked:bg-primary-50 dark:peer-checked:bg-primary-900/20 text-center transition-all hover:border-primary-300 dark:hover:border-primary-700 hover:shadow-md">
                                <div class="w-12 h-12 mx-auto bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-3 peer-checked:bg-white dark:peer-checked:bg-primary-800/50 peer-checked:shadow-sm group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6 text-gray-500 dark:text-gray-400 peer-checked:text-primary-600 dark:peer-checked:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 2l-3 2v3l2 1v4h10V8l2-1V4l-3-2H8z M12 2v3 M8 13h8l1 9h-3l-2-5-2 5H7l1-9z"/></svg>
                                </div>
                                <p class="text-sm font-bold text-gray-800 dark:text-gray-200 peer-checked:text-primary-700 dark:peer-checked:text-primary-400">Full Body</p>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wider">Suits, Dresses</p>
                            </div>
                        </label>
                        <!-- Custom (Original Custom Icon) -->
                        <label class="cursor-pointer group">
                            <input type="radio" name="garment_category" value="other" x-model="garmentCategory" required class="sr-only peer">
                            <div class="p-4 border-2 border-gray-200 dark:border-gray-600 rounded-2xl peer-checked:border-primary-500 peer-checked:bg-primary-50 dark:peer-checked:bg-primary-900/20 text-center transition-all hover:border-primary-300 dark:hover:border-primary-700 hover:shadow-md">
                                <div class="w-12 h-12 mx-auto bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-3 peer-checked:bg-white dark:peer-checked:bg-primary-800/50 peer-checked:shadow-sm group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6 text-gray-500 dark:text-gray-400 peer-checked:text-primary-600 dark:peer-checked:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                                </div>
                                <p class="text-sm font-bold text-gray-800 dark:text-gray-200 peer-checked:text-primary-700 dark:peer-checked:text-primary-400">Custom</p>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wider">Other Designs</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div x-show="garmentCategory!==''" x-transition class="pt-4 border-t border-gray-100 dark:border-gray-700">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Specific Garment Name <span class="text-red-500">*</span></label>
                <input type="text" name="specific_garment" x-model="specificGarment" required placeholder="e.g. Slim-fit Linen Shirt" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 p-3 text-sm focus:ring-2 focus:ring-primary-500 outline-none">
            </div>
            
            <div x-show="['top','full','other'].includes(garmentCategory)" x-transition class="bg-gray-50 dark:bg-gray-700/40 rounded-xl p-5 border border-gray-100 dark:border-gray-700 border-l-4 border-l-primary-500">
                <h3 class="font-semibold text-gray-800 dark:text-white flex items-center gap-2 mb-4">Upper Body <span class="text-xs text-primary-600 bg-primary-100 dark:bg-primary-900/30 px-2 py-0.5 rounded-full">Inches</span></h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach(['collar'=>'chest','chest'=>'shoulder','shoulder'=>'sleeve_length','sleeve_length'=>'full_length','full_length'=>'armhole','armhole'=>'upper_waist','upper_waist'=>''] as $field=>$next)
                    <div><label class="text-xs text-gray-500 block mb-1">{{ ucwords(str_replace('_',' ',$field)) }}</label><input type="number" id="{{ $field }}" name="{{ $field }}" step="0.25" min="0" placeholder="0.0" @if($next) @input="focusNext($event,'{{ $next }}')" @endif class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 p-2.5 text-sm focus:ring-1 focus:ring-primary-500"></div>
                    @endforeach
                </div>
            </div>
            
            <div x-show="['bottom','full','other'].includes(garmentCategory)" x-transition class="bg-gray-50 dark:bg-gray-700/40 rounded-xl p-5 border border-gray-100 dark:border-gray-700 border-l-4 border-l-amber-500 mt-6">
                <h3 class="font-semibold text-gray-800 dark:text-white flex items-center gap-2 mb-4">Lower Body <span class="text-xs text-amber-600 bg-amber-100 dark:bg-amber-900/30 px-2 py-0.5 rounded-full">Inches</span></h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach(['lower_waist'=>'hip','hip'=>'thigh','thigh'=>'outseam','outseam'=>'inseam','inseam'=>'bottom_hem','bottom_hem'=>'knee','knee'=>''] as $field=>$next)
                    <div><label class="text-xs text-gray-500 block mb-1">{{ ucwords(str_replace('_',' ',$field)) }}</label><input type="number" id="{{ $field }}" name="{{ $field }}" step="0.25" min="0" placeholder="0.0" @if($next) @input="focusNext($event,'{{ $next }}')" @endif class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 p-2.5 text-sm focus:ring-1 focus:ring-amber-500"></div>
                    @endforeach
                </div>
            </div>
            
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6 space-y-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Design & Fabric Studio</h3>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Fabric Source</label>
                        <input type="hidden" name="fabric_source" x-model="fabricSource"><input type="hidden" name="fabric_name" x-model="selectedShopFabric">
                        <div class="grid grid-cols-2 gap-3">
                            <label class="cursor-pointer"><input type="radio" name="fabric" value="shop" x-model="fabricSource" @click="fabricModalOpen=true; manageMode=false;" class="sr-only peer"><div class="p-4 border-2 border-gray-200 dark:border-gray-600 rounded-lg peer-checked:border-primary-500 peer-checked:bg-primary-50 dark:peer-checked:bg-primary-900/20 text-center transition"><svg class="w-8 h-8 mx-auto text-gray-400 peer-checked:text-primary-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg><p class="text-sm font-medium text-gray-700 dark:text-gray-300">Shop Fabric</p></div></label>
                            <label class="cursor-pointer"><input type="radio" name="fabric" value="customer" x-model="fabricSource" @click="selectedShopFabric='';selectedShopFabricImage=''" class="sr-only peer"><div class="p-4 border-2 border-gray-200 dark:border-gray-600 rounded-lg peer-checked:border-primary-500 peer-checked:bg-primary-50 dark:peer-checked:bg-primary-900/20 text-center transition"><svg class="w-8 h-8 mx-auto text-gray-400 peer-checked:text-primary-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg><p class="text-sm font-medium text-gray-700 dark:text-gray-300">Customer's</p></div></label>
                        </div>
                        <div x-show="fabricSource==='shop'&&selectedShopFabric!==''" class="mt-3 p-3 bg-primary-50 dark:bg-primary-900/20 rounded-lg border border-primary-200 dark:border-primary-800"><div class="flex items-center gap-3"><img x-show="selectedShopFabricImage" :src="selectedShopFabricImage" class="w-12 h-12 rounded-lg object-cover border"><div><p class="text-sm font-bold text-primary-800 dark:text-primary-200" x-text="selectedShopFabric"></p><p class="text-xs text-primary-600 dark:text-primary-400">In Stock</p></div></div></div>
                        <div x-show="fabricSource==='customer'" class="mt-3 p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800"><p class="text-sm font-medium text-amber-800 dark:text-amber-200">Customer Provided Fabric</p><p class="text-xs text-amber-600 dark:text-amber-400 mt-1">Fabric will be brought by the customer</p></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Reference Image</label>
                        <div class="relative aspect-square rounded-lg border-2 border-dashed flex items-center justify-center cursor-pointer transition overflow-hidden" :class="isDragging?'border-primary-500 bg-primary-50 dark:bg-primary-900/20':'border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'" @dragover.prevent="isDragging=true" @dragleave.prevent="isDragging=false" @drop.prevent="handleDrop($event)" @click="$refs.fileInput.click()">
                            <input type="file" name="design_image" x-ref="fileInput" class="hidden" @change="handleFileChange($event)" accept="image/*">
                            <template x-if="!imagePreview"><div class="text-center p-4"><svg class="w-10 h-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg><p class="text-sm font-medium text-gray-500">Click or drag to upload</p></div></template>
                            <template x-if="imagePreview"><img :src="imagePreview" class="absolute inset-0 w-full h-full object-cover"></template>
                        </div>
                        <button type="button" x-show="imagePreview" @click="imagePreview=null;$refs.fileInput.value=''" class="mt-2 text-xs text-red-500">Remove</button>
                    </div>
                </div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Styling Notes</label><textarea name="styling_notes" rows="3" placeholder="E.g. Double stitching..." class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 p-3 text-sm focus:ring-2 focus:ring-primary-500 outline-none resize-none"></textarea></div>
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button type="reset" class="px-5 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium text-sm">Clear</button>
                <button type="submit" class="px-6 py-2.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-medium text-sm">Save Order</button>
            </div>
        </form>
    </div>

    <!-- Fabric Modal -->
    <div x-show="fabricModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" x-cloak x-trap="fabricModalOpen" @keydown.escape.window="fabricModalOpen=false;if(selectedShopFabric==='')fabricSource=''; manageMode=false;">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 w-full max-w-4xl shadow-xl max-h-[85vh] overflow-y-auto mx-4 custom-scrollbar" @click.away="fabricModalOpen=false;if(selectedShopFabric==='')fabricSource=''; manageMode=false;">
            
            <div class="flex justify-between items-center mb-6 border-b border-gray-100 dark:border-gray-700 pb-4">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white" x-text="manageMode ? 'Manage Fabric Inventory' : 'Select Shop Fabric'"></h3>
                <div class="flex items-center gap-4">
                    <button @click="manageMode = !manageMode" class="text-xs font-bold px-4 py-2 rounded-lg transition" :class="manageMode ? 'bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-600' : 'bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300 hover:bg-primary-200 dark:hover:bg-primary-900/50'" x-text="manageMode ? '← Back to Selection' : 'Manage Inventory ⚙️'"></button>
                    <button @click="fabricModalOpen=false;if(selectedShopFabric==='')fabricSource=''; manageMode=false;" class="text-gray-400 hover:text-gray-600 dark:hover:text-white p-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <!-- TAB 1: Select Fabric View -->
            <div x-show="!manageMode" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                <div class="mb-6">
                    <h4 class="text-sm font-semibold text-emerald-600 mb-3">In Stock</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <template x-for="(fabric,index) in inStockFabrics()" :key="index">
                            <div @click="selectFabric(fabric.name,fabric.image)" class="cursor-pointer border-2 rounded-xl overflow-hidden hover:border-primary-500 transition-all hover:shadow-md" :class="selectedShopFabric===fabric.name?'border-primary-500 bg-primary-50 dark:bg-primary-900/20':'border-gray-200 dark:border-gray-700'">
                                <img :src="fabric.image" class="w-full h-28 object-cover">
                                <div class="p-2.5 text-center">
                                    <p class="text-xs font-bold text-gray-800 dark:text-gray-200" x-text="fabric.name"></p>
                                    <p class="text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 mt-0.5">In Stock</p>
                                </div>
                            </div>
                        </template>
                        <div x-show="inStockFabrics().length === 0" class="col-span-full py-4 text-center text-sm text-gray-500">No fabrics in stock.</div>
                    </div>
                </div>
                <div class="mb-2">
                    <h4 class="text-sm font-semibold text-red-500 mb-3">Out of Stock</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <template x-for="(fabric,index) in outOfStockFabrics()" :key="'oos-'+index">
                            <div class="border-2 rounded-xl overflow-hidden border-gray-200 dark:border-gray-700 opacity-50">
                                <img :src="fabric.image" class="w-full h-28 object-cover grayscale">
                                <div class="p-2.5 text-center">
                                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400" x-text="fabric.name"></p>
                                    <p class="text-[10px] font-semibold text-red-500 mt-0.5">Out of Stock</p>
                                </div>
                            </div>
                        </template>
                        <div x-show="outOfStockFabrics().length === 0" class="col-span-full py-4 text-center text-sm text-gray-500">No out of stock fabrics.</div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: Manage Fabrics View -->
            <div x-show="manageMode" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Complete Fabric List</h4>
                    <button @click="showAddFabric=!showAddFabric" class="text-xs bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2 rounded-lg transition shadow-sm">+ Add New Fabric</button>
                </div>
                
                <div x-show="showAddFabric" x-collapse class="mb-5 p-5 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1">Fabric Name</label>
                            <input type="text" x-model="newFabricName" placeholder="e.g. Italian Silk" class="w-full p-2.5 rounded-lg border border-gray-300 dark:border-gray-600 text-sm bg-white dark:bg-gray-800 outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1">Fabric Image</label>
                            <input type="file" x-ref="newFabricFile" @change="handleNewFabricImage($event)" accept="image/*" class="w-full p-1.5 rounded-lg border border-gray-300 dark:border-gray-600 text-sm bg-white dark:bg-gray-800 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-primary-50 file:text-primary-700 dark:file:bg-primary-900/30 dark:file:text-primary-300 cursor-pointer">
                        </div>
                    </div>
                    <button @click="addFabric()" class="mt-4 w-full bg-gray-800 dark:bg-gray-200 hover:bg-gray-900 dark:hover:bg-white text-white dark:text-gray-900 font-bold py-2.5 rounded-lg text-sm transition shadow-sm">Save to Inventory</button>
                </div>
                
                <div class="space-y-2 max-h-[50vh] overflow-y-auto pr-2 custom-scrollbar">
                    <div x-show="fabrics.length === 0" class="text-center py-6 text-gray-500 text-sm">Inventory is empty. Add a new fabric above.</div>
                    
                    <template x-for="(fabric,index) in fabrics" :key="'mgmt-'+index">
                        <div class="flex items-center justify-between p-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600 transition">
                            <div class="flex items-center gap-4">
                                <label class="relative group cursor-pointer flex-shrink-0 w-10 h-10 rounded-lg overflow-hidden border border-gray-100 dark:border-gray-700 block bg-gray-100 dark:bg-gray-700">
                                    <img :src="fabric.image" class="w-full h-full object-cover group-hover:opacity-40 transition-opacity">
                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <svg class="w-4 h-4 text-gray-800 dark:text-white drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </div>
                                    <input type="file" @change="updateFabricImage($event, fabric)" accept="image/*" class="hidden">
                                </label>
                                <span class="text-sm font-bold text-gray-800 dark:text-gray-200" x-text="fabric.name"></span>
                            </div>
                            <div class="flex items-center gap-3">
                                <button @click="toggleStock(fabric)" :class="fabric.inStock?'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800/30':'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800/30'" class="px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider border transition-colors w-24 text-center">
                                    <span x-text="fabric.inStock ? 'In Stock' : 'Out of Stock'"></span>
                                </button>
                                <button @click="removeFabric(fabric, index)" class="text-red-400 hover:text-red-600 dark:hover:text-red-400 bg-red-50 dark:bg-red-900/10 p-2 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <div id="loading-overlay" class="fixed inset-0 z-[100] hidden bg-gray-900/80 backdrop-blur-sm flex items-center justify-center">
        <div class="flex flex-col items-center">
            <div class="w-20 h-20 bg-white rounded-full overflow-hidden flex items-center justify-center animate-scissor-cut mb-4 shadow-2xl border-4 border-white/20">
                <img src="/images/logo.jpeg" alt="Loading..." class="w-full h-full object-cover">
            </div>
            <h3 class="text-xl font-bold text-white mt-2">Saving Order...</h3>
        </div>
    </div>
    <style>
        @keyframes scissor-cut { 0%,100% { transform: scale(1) rotate(0deg); } 50% { transform: scale(1.1) rotate(15deg); } }
        .animate-scissor-cut { animation: scissor-cut 1s ease-in-out infinite; }
    </style>
</div>
@endsection