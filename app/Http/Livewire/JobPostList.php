<?php

namespace App\Http\Livewire;

use App\Models\JobPost;
use Livewire\Component;
use Livewire\WithPagination;

class JobPostList extends Component
{
    use WithPagination;

    public $tags = [];

    public function addTag($tag)
    {
        if (isset($this->tags[$tag['id']])) {
            return;
        }

        $this->tags[$tag['id']] = $tag;
        $this->resetPage();
    }

    public function removeTag($id)
    {
        unset($this->tags[$id]);
        $this->resetPage();
    }

    public function render()
    {
        $query = JobPost::query();
        foreach ($this->tags as $tag) {
            $query->whereHas('tags', function ($query) use ($tag) {
                $query->where('id', $tag);
            });
        }

        return view('livewire.job-post-list', [
            'jobPosts' => $query->orderBy('source_created_at', 'desc')->simplePaginate(30),
        ]);
    }
}
