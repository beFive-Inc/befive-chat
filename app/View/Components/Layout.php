<?php

namespace App\View\Components;

use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Layout extends Component
{
    public Collection $friends;
    public Collection $requestFriends;
    public Collection $medias;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(Collection $friends, Collection $requestFriends, Collection $medias)
    {
        $this->friends = $friends;
        $this->requestFriends = $requestFriends;
        $this->medias = $medias;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.layout.layout');
    }
}
