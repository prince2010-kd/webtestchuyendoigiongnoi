
<div class="" style="padding-left: {{ $depth * 20 }}px;">
    @foreach ($menus as $menuOption)
        @php
            $permission = $permissionsMap[$menuOption->id] ?? null;
        @endphp
        <!-- <div class="border-bottom"> -->
            <div class="py-2 mb-1 text-primary fw-bold border-bottom">
                <div class=" d-flex justify-content-between align-items-center">
                    ●&nbsp;{{$menuOption->title}} 
                    
                    <div class="text-black">
                        <input type="checkbox" class="form-check-input check-all" id="check-all-{{$menuOption->id}}"/>
                        <label class="fw-normal">Chọn tất cả</label>
                    </div>
                </div> 
            </div>

        <div class="row py-2">
            <!-- <div class="col-md-2 col-sm-4 mb-2">
                <input type="checkbox" 
                class="form-check-input" 
                id="view-checkbox-{{$menuOption->id}}" 
                name="permissions[can_view][{{ $menuOption->id }}]"
                {{ $permission && $permission->can_view ? 'checked' : '' }}
                />
                <label for="view-checkbox-{{$menuOption->id}}">View</label>
            </div>
            <div class="col-md-2 col-sm-4 mb-2">
                <input 
                type="checkbox" 
                class="form-check-input" 
                id="add-checkbox-{{$menuOption->id}}" 
                name="permissions[can_add][{{ $menuOption->id }}]"
                {{ $permission && $permission->can_add ? 'checked' : '' }}
                />
                <label for="add-checkbox-{{$menuOption->id}}">Add</label>
            </div>
            <div class="col-md-2 col-sm-4 mb-2">
                <input 
                type="checkbox" 
                class="form-check-input" 
                id="edit-checkbox-{{$menuOption->id}}" 
                name="permissions[can_edit][{{ $menuOption->id }}]"
                {{ $permission && $permission->can_edit ? 'checked' : '' }}
                />
                <label for="edit-checkbox-{{$menuOption->id}}">Edit</label>
            </div>
            <div class="col-md-2 col-sm-4 mb-2">
                <input 
                type="checkbox" 
                class="form-check-input" 
                id="delete-checkbox-{{$menuOption->id}}" 
                name="permissions[can_delete][{{ $menuOption->id }}]"
                {{ $permission && $permission->can_delete ? 'checked' : '' }}
                />
                <label for="delete-checkbox-{{$menuOption->id}}">Delete</label>
            </div>
            <div class="col-md-2 col-sm-4 mb-2">
                <input 
                type="checkbox" 
                class="form-check-input" 
                id="export-checkbox-{{$menuOption->id}}" 
                name="permissions[can_export][{{ $menuOption->id }}]"
                {{ $permission && $permission->can_export ? 'checked' : '' }}
                />
                <label for="export-checkbox-{{$menuOption->id}}">Export</label>
            </div> -->

            @foreach ($permissionTypes as $type)
                <div class="col-md-2 col-sm-4 mb-2">
                    <input
                        type="checkbox"
                        class="form-check-input"
                        id="{{ $type }}-checkbox-{{ $menuOption->id }}"
                        name="permissions[{{ $type }}][{{ $menuOption->id }}]"
                        {{ $permission && $permission->$type ? 'checked' : '' }}
                    />
                    <label for="{{ $type }}-checkbox-{{ $menuOption->id }}">
                        {{ ucfirst(str_replace('can_', '', $type)) }}
                    </label>
                </div>
            @endforeach
        </div>
        @if($menuOption->children && $menuOption->children->isNotEmpty())
            @include('menu-auth', [
                'menus' => $menuOption->children,
                'selected' => $selected ?? null,
                'depth' => $depth+1,
            ])
        @endif
    @endforeach
</div>
