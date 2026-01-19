<?php

namespace App\Livewire\Admin\Staff;

use App\Models\Staff;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\File;

class StaffIndex extends Component
{
    use WithPagination, WithFileUploads;

    // Search & Filter
    public $search = '';
    
    // Modal states
    public $showModal = false;
    public $showDeleteModal = false;
    public $editMode = false;
    
    // Form data
    public $staffId;
    public $name = '';
    public $position = '';
    public $department = '';
    public $photo;
    public $currentPhoto = '';
    public $is_active = true;
    public $sort_order = 0;
    
    // Delete confirmation
    public $deleteId;
    public $deleteName;

    protected $queryString = ['search'];

    protected function rules()
    {
        return [
            'name' => 'required|max:255',
            'position' => 'required|max:255',
            'department' => 'nullable|max:255',
            'photo' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ];
    }

    protected $messages = [
        'name.required' => 'กรุณากรอกชื่อ-นามสกุล',
        'name.max' => 'ชื่อ-นามสกุลต้องไม่เกิน 255 ตัวอักษร',
        'position.required' => 'กรุณากรอกตำแหน่ง',
        'position.max' => 'ตำแหน่งต้องไม่เกิน 255 ตัวอักษร',
        'department.max' => 'หน่วยงานต้องไม่เกิน 255 ตัวอักษร',
        'photo.image' => 'ไฟล์ต้องเป็นรูปภาพ',
        'photo.max' => 'ขนาดรูปภาพต้องไม่เกิน 2MB',
        'sort_order.integer' => 'ลำดับต้องเป็นตัวเลข',
        'sort_order.min' => 'ลำดับต้องไม่ติดลบ',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->editMode = false;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $staff = Staff::findOrFail($id);
        
        $this->resetForm();
        $this->editMode = true;
        $this->staffId = $staff->id;
        $this->name = $staff->name;
        $this->position = $staff->position;
        $this->department = $staff->department;
        $this->currentPhoto = $staff->photo;
        $this->is_active = $staff->is_active;
        $this->sort_order = $staff->sort_order;
        
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'position' => $this->position,
            'department' => $this->department,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
        ];

        // Handle photo upload (Save to public/uploads for shared hosting)
        if ($this->photo) {
            // Delete old photo if editing
            if ($this->editMode && $this->currentPhoto) {
                $oldPath = public_path($this->currentPhoto);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }
            
            // Create uploads directory if not exists
            $uploadPath = public_path('uploads/staff-photos');
            if (!File::isDirectory($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }
            
            // Generate unique filename and save
            $ext = $this->photo->getClientOriginalExtension();
            $name = pathinfo($this->photo->getClientOriginalName(), PATHINFO_FILENAME);
            // Sanitize filename to strict alphanumeric characters
            $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '', str_replace(' ', '_', $name));
            $filename = time() . '_' . $safeName . '.' . $ext;
            
            // Use copy instead of move for better shared hosting compatibility
            File::copy($this->photo->getRealPath(), $uploadPath . '/' . $filename);
            $data['photo'] = 'uploads/staff-photos/' . $filename;
        }

        if ($this->editMode) {
            $staff = Staff::findOrFail($this->staffId);
            $staff->update($data);
            session()->flash('success', 'แก้ไขข้อมูลผู้ปฏิบัติงานสำเร็จ');
        } else {
            Staff::create($data);
            session()->flash('success', 'เพิ่มผู้ปฏิบัติงานสำเร็จ');
        }

        $this->closeModal();
    }

    public function confirmDelete($id)
    {
        $staff = Staff::findOrFail($id);
        $this->deleteId = $staff->id;
        $this->deleteName = $staff->name;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $staff = Staff::findOrFail($this->deleteId);
        
        // Delete photo from public folder
        if ($staff->photo) {
            $photoPath = public_path($staff->photo);
            if (File::exists($photoPath)) {
                File::delete($photoPath);
            }
        }
        
        $staff->delete();
        
        session()->flash('success', 'ลบผู้ปฏิบัติงานสำเร็จ');
        $this->showDeleteModal = false;
        $this->deleteId = null;
        $this->deleteName = null;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deleteId = null;
        $this->deleteName = null;
    }

    private function resetForm()
    {
        $this->reset(['staffId', 'name', 'position', 'department', 'photo', 'currentPhoto', 'is_active', 'sort_order']);
        $this->is_active = true;
        $this->sort_order = 0;
        $this->resetValidation();
    }

    public function render()
    {
        $staff = Staff::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('position', 'like', '%' . $this->search . '%')
                      ->orWhere('department', 'like', '%' . $this->search . '%');
            })
            ->ordered()
            ->paginate(10);

        return view('livewire.admin.staff.staff-index', [
            'staffList' => $staff,
        ])->layout('layouts.app');
    }
}
