<?php

namespace Trafikrak\Storefront\Livewire\Media;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Trafikrak\Models\Education\Topic;
use Trafikrak\Models\Media\Audio;
use Trafikrak\Models\Media\Video;
use Trafikrak\Models\Media\Visibility;

class SearchPage extends Page
{
    use WithPagination;

    #[Url]
    public string $q = '';

    #[Url]
    public string $c = '';

    #[Url]
    public string $t = '';

    private array $columns = ['id', 'name', 'description', 'source', 'source_id', 'created_at'];

    public function render(): View
    {
        $topics = Topic::where('is_published', true)
            ->with([
                'media',
                'defaultUrl',
            ])
            ->get();

        $videosQuery = Video::query()
            ->select([...$this->columns, DB::raw("'videos' as type")])
            ->where('is_published', true)
            ->where('visibility', Visibility::PUBLIC->value)
            ->when($this->q, function ($query) {
                $videosByQuery = Video::search($this->q)->get();
                $query->whereIn('id', $videosByQuery->pluck('id'));
            });

        $audiosQuery = Audio::query()
            ->select([...$this->columns, DB::raw("'audios' as type")])
            ->where('is_published', true)
            ->where('visibility', Visibility::PUBLIC->value)
            ->when($this->q, function ($query) {
                $audiosByQuery = Audio::search($this->q)->get();
                $query->whereIn('id', $audiosByQuery->pluck('id'));
            });

        $media = $videosQuery
            ->union($audiosQuery)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('trafikrak::storefront.livewire.media.search', compact('topics', 'media'))
            ->title(__('Audios y vídeos'));
    }

    public function search(): void
    {
        $this->resetPage();
    }
}
