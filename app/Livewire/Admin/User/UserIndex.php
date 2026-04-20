<?php

namespace App\Livewire\Admin\User;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\WithPagination;

class UserIndex extends Component
{
    use WithPagination; // Gunakan ini agar halaman tidak berat jika user banyak

    // Variabel Form
    public $id_user, $nama_lengkap, $email, $password, $ROLE, $jabatan;
    public $status_user = 'Aktif'; // Default value
    public $can_manage_master = false;
    // Variabel ID untuk Edit
    public $user_id_to_edit = null;

    // Variabel Kontrol Modal
    public $isModalOpen = false;

    // Rules Validasi
   protected function rules()
    {
        return [
            'nama_lengkap' => 'required',
            'email' => 'required|email|unique:users,email,' . $this->user_id_to_edit . ',id_user',
            'ROLE' => 'required',
            'status_user' => 'required|in:Aktif,Nonaktif',
            'jabatan' => 'nullable',
            'password' => $this->user_id_to_edit ? 'nullable|min:6' : 'required|min:6',
            'can_manage_master' => 'boolean', // <--- TAMBAHKAN BARIS INI
        ];
    }

    public function render()
    {
        return view('livewire.admin.user.user-index', [
            'users' => User::latest()->paginate(10) // Saya ubah ke paginate biar rapi
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $user = User::where('id_user', $id)->firstOrFail();

        $this->user_id_to_edit = $id;
        $this->id_user = $user->id_user; // Primary Key
        $this->nama_lengkap = $user->nama_lengkap;
        $this->email = $user->email;
        $this->ROLE = $user->ROLE;
        $this->status_user = $user->status_user; // Load status dari DB
        $this->jabatan = $user->jabatan;
        $this->can_manage_master = (bool)$user->can_manage_master;

        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->id_user = '';
        $this->nama_lengkap = '';
        $this->email = '';
        $this->password = '';
        $this->ROLE = '';
        $this->status_user = 'Aktif'; // Reset kembali ke Aktif
        $this->jabatan = '';
        $this->can_manage_master = false;
        $this->user_id_to_edit = null;
        $this->resetErrorBag();
    }

    public function store()
    {
        $this->validate();

        // TAMBAHAN PENGAMAN: Jika bukan Logistik, otomatis matikan izinnya
        if ($this->ROLE !== 'Logistik') {
            $this->can_manage_master = false;
        }

        $data = [
            'nama_lengkap' => $this->nama_lengkap,
            'email' => $this->email,
            'ROLE' => $this->ROLE,
            'status_user' => $this->status_user,
            'jabatan' => $this->jabatan,
            'can_manage_master' => $this->can_manage_master, // Pastikan ini ada
        ];

        if (!empty($this->password)) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($this->password);
        }

        User::updateOrCreate(['id_user' => $this->user_id_to_edit], $data);

        session()->flash('message', $this->user_id_to_edit ? 'User berhasil diupdate' : 'User berhasil dibuat');
        $this->closeModal();
    }

    public function delete($id)
    {
        $user = User::where('id_user', $id)->first();

        if (!$user) {
            session()->flash('message', 'User tidak ditemukan');
            return;
        }

        if (auth()->user()->id_user == $user->id_user) {
            session()->flash('message', 'Tidak bisa menghapus akun sendiri');
            return;
        }

        $user->delete();
        session()->flash('message', 'User berhasil dihapus');
    }
}