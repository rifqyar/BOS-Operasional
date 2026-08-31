<?php

namespace App\Livewire\Operation;

use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view('livewire.operation.index')
            ->layout('layouts.operation');
    }
}