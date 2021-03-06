<?php

// TODO: search-bar actions

namespace App\Http\Livewire;

use App\Constant\ChatroomStatus;
use App\Constant\ChatroomUserStatus;
use App\Constant\ChatroomType;
use App\Models\Chatroom;
use App\Models\ChatroomUser;
use App\Models\User;
use App\Traits\Chatroom as ChatroomHelper;
use App\Traits\Operator;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Component;

class ChatroomCreate extends Component
{
    use Operator;
    use ChatroomHelper;

    public Collection $allChatroom;
    public bool $isCanal = false;
    public bool $isPublic = true;

    public Collection $friendList;
    public Collection $rememberKeys;
    public Collection $rememberPreSelectKeys;
    public Collection $selectedFriends;
    public Collection $preSelectedFriends;
    public bool $preSelectedFriendsAreRequired = false;

    public string $query = '';
    public string $name = '';
    public string $error = '';

    public $listeners = [
        'friendRefresh' => 'refreshFriends'
    ];

    public function mount()
    {
        $this->selectedFriends = collect();
        $this->rememberKeys = collect();
        $this->preSelectedFriends = $this->preSelectedFriends ?? collect();

        if ($this->preSelectedFriends->count()) {
            $this->preSelectedFriends = $this->removeOwnUser();
            $this->getUserFromPreselected();
        }
    }

    public function refreshFriends()
    {
        $this->friendList = auth()->user()
            ->getFriends();
    }

    /**
     * @return void
     */
    public function getUserFromPreselected(): void
    {
        $this->preSelectedFriends = $this->preSelectedFriends->map(function ($author) {
            return $author->user;
        });
    }

    /**
     * @return Collection
     */
    public function removeOwnUser(): Collection
    {
         return $this->preSelectedFriends->filter(function ($author) {
            return $author->user->id != auth()->id();
        });
    }

    /**
     * @return void
     */
    public function addOwnUser(): void
    {
        $this->selectedFriends->push(auth()->user());
    }

    /**
     * @param User $friend
     * @return void
     */
    public function addOtherFriendToSelectedFriend(User $friend)
    {
        $addFriend = User::find($friend->id);
        $this->friendList->push($addFriend);

        $this->friendList->search(function ($friend, $key) use ($addFriend) {
            if ($friend->id === $addFriend->id) {
                $this->rememberKeys->put($key, $key);
                $this->setKey($key,'key', $key);
                if ($this->preSelectedFriendsAreRequired) {
                    $this->setKey($key, 'isRequired', true);
                }
            }
        });
    }

    /**
     * @return void
     */
    public function checkIfPreSelectedIsinFriendList(): void
    {
        foreach ($this->preSelectedFriends as $preFriend) {
            $check = $this->friendList->contains(function ($friend, $key) use ($preFriend) {
                if ($preFriend->user->id === $friend->id) {
                    $this->rememberKeys->put($key, $key);
                    $this->setKey($key, 'key', $key);
                    if ($this->preSelectedFriendsAreRequired) {
                        $this->setKey($key, 'isRequired', true);
                    }
                }
                return $preFriend->user->id === $friend->id;
            });
            if (!$check) {
                $this->addOtherFriendToSelectedFriend($preFriend->user);
            }
        }
    }

    /**
     * @return Collection
     */
    protected function getSortingFriends(): Collection
    {
        return $this->friendList;
    }

    /**
     * @return Collection
     */
    public function getSearchingFriends(): Collection
    {
        return $this->friendList->filter(function ($friend) {
            return $this->likeOperator("%$this->query%", $friend->pseudo);
        });
    }

    /**
     * @param int $key
     * @return void
     */
    public function toggleToChatroom(int $key)
    {
        if ($this->isInTheChatroom($key)) {
            $this->removeFromChatroom($key);
        } else {
            $this->addToChatroom($key);
        }
    }

    /**
     * @param int $key
     * @return void
     */
    public function addToChatroom(int $key): void
    {
        $this->rememberKeys->put($key, $key);
        $this->setKey($key, 'key', $key);
        $this->resetError();
    }

    /**
     * @param int $key
     * @return void
     */
    public function removeFromChatroom(int $key): void
    {
        $this->rememberKeys->pull($key);
        $this->setKey($key, 'key', null);
        $this->resetError();
    }

    /**
     * @param int $key
     * @param $value
     * @return void
     */
    public function setKey(int $key, string $attribute, $value): void
    {
        $this->friendList[$key]->{$attribute} = $value;
    }

    /**
     * @return void
     */
    public function resetError(): void
    {
        $this->error = '';
    }

    /**
     * @return void
     */
    public function refreshSelectedFriend(): void
    {
        $this->selectedFriends = $this->friendList->filter(function ($friend, $key) {
            return $this->rememberKeys->filter(function ($f, $k) use ($key) {
                $this->setKey($k, 'key', $k);
                return $k === $key;
            })->count();
        });
    }

    /**
     * @return false
     */
    public function createChatroom(): bool
    {
        $this->resetError();
        $this->addOwnUser();
        $isAlreadyAChatroom = collect([]);

        if (!$this->isCanal) {
            $isAlreadyAChatroom = $this->checkIfThisChatroomExist($this->allChatroom, $this->selectedFriends);
        }

        if ($this->isCanal && empty($this->name)) {
            $this->error = __('validation.chatroom.create.name');
        } elseif ($isAlreadyAChatroom->count()) {
            $this->redirect(route('chatroom.show', $isAlreadyAChatroom->first()->uuid));
        } elseif($this->selectedFriends->count() > 1) {
            $chatroom = Chatroom::create([
                'uuid' => Str::uuid(),
                'name' => !empty($this->name) ? $this->name : null,
                'type' => $this->isCanal ? ChatroomType::CANAL : null,
                'status' => $this->isCanal && $this->isPublic ? ChatroomStatus::PUBLIC : ChatroomStatus::PRIVATE,
            ]);

            foreach ($this->selectedFriends as $friend) {
                ChatroomUser::create([
                    'chatroom_id' => $chatroom->id,
                    'user_id' => $friend->id,
                    'status' => $this->isCanal && $friend->id != auth()->id() ? ChatroomUserStatus::PENDING :ChatroomUserStatus::ACCEPTED,
                    'view_at' => Carbon::now()
                ]);
            }

            $this->emit('refreshChatrooms');

            $this->redirect(route('chatroom.show', $chatroom->uuid));
        } else {
            $this->error = __('validation.chatroom.create');
        }

        return false;
    }

    /**
     * @param $key
     * @return bool
     */
    public function isInTheChatroom($key): bool
    {
        if (isset($this->rememberKeys[$key])) {
            return $this->rememberKeys[$key] === $key;
        }

        return false;
    }

    /**
     * @return void
     */
    public function createCanal()
    {
        $this->isCanal = true;
    }

    /**
     * @return void
     */
    public function createGroup()
    {
        $this->isCanal = false;
    }

    public function toggleCanal()
    {
        if ($this->isCanal === true) {
            $this->createGroup();
        } else {
            $this->createCanal();
        }
    }

    public function setIsPublic()
    {
        $this->isPublic = true;
    }

    /**
     * @return void
     */
    public function unsetIsPublic()
    {
        $this->isPublic = false;
    }

    public function togglePublic()
    {
        if ($this->isPublic === true) {
            $this->unsetIsPublic();
        } else {
            $this->setIsPublic();
        }
    }

    public function render()
    {
        $this->refreshSelectedFriend();

        if ($this->query) {
            $friends = $this->getSearchingFriends();
        } else {
            $friends = $this->getSortingFriends();
        }

        return view('livewire.chatroom-create', [
            'friends' => $friends,
        ]);
    }
}
