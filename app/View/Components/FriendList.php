<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FriendList extends Component
{
    public $friends;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($friends)
    {
        $this->friends = $friends;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.friend-list');
    }
}
