<?php

namespace App\Http\Controllers;

use App\Jobs\SyncLocationUpvotesCount;
use App\Models\Film;
use App\Models\Location;
use App\Models\LocationVote;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(Request $request): View
    {
        $city = trim((string) $request->query('city', ''));
        $country = trim((string) $request->query('country', ''));
        $sort = (string) $request->query('sort', 'recent');

        if (! in_array($sort, ['recent', 'most_upvoted', 'least_upvoted'], true)) {
            $sort = 'recent';
        }

        $query = Location::query()
            ->with(['film', 'user'])
            ->when($city !== '', fn ($query) => $query->where('city', 'like', '%'.$city.'%'))
            ->when($country !== '', fn ($query) => $query->where('country', 'like', '%'.$country.'%'));

        if ($sort === 'most_upvoted') {
            $query->orderByDesc('upvotes_count')->orderByDesc('created_at');
        } elseif ($sort === 'least_upvoted') {
            $query->orderBy('upvotes_count')->orderByDesc('created_at');
        } else {
            $query->orderByDesc('created_at');
        }

        $locations = $query->get();

        return view('locations.index', [
            'locations' => $locations,
            'filters' => [
                'city' => $city,
                'country' => $country,
                'sort' => $sort,
            ],
        ]);
    }

    public function create(): View
    {
        $films = Film::query()->orderBy('title')->get();

        return view('locations.create', compact('films'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'film_id' => ['required', 'exists:films,id'],
            'name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:4096'],
        ]);

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('locations', 'public');
        }

        unset($data['photo']);

        $data['user_id'] = $request->user()->id;
        $data['upvotes_count'] = 0;

        Location::query()->create($data);

        return redirect()->route('locations.index')->with('ok', 'Emplacement créé.');
    }

    public function show(Request $request, Location $location): View
    {
        $location->load(['film', 'user']);

        $hasVoted = LocationVote::query()
            ->where('user_id', $request->user()->id)
            ->where('location_id', $location->id)
            ->exists();

        return view('locations.show', compact('location', 'hasVoted'));
    }

    public function toggleVote(Request $request, Location $location): JsonResponse|RedirectResponse
    {
        $vote = LocationVote::query()
            ->where('user_id', $request->user()->id)
            ->where('location_id', $location->id)
            ->first();

        if ($vote) {
            $vote->delete();
        } else {
            LocationVote::query()->create([
                'user_id' => $request->user()->id,
                'location_id' => $location->id,
            ]);
        }

        SyncLocationUpvotesCount::dispatch($location->id);

        $upvotesCount = LocationVote::query()
            ->where('location_id', $location->id)
            ->count();
        $hasVoted = LocationVote::query()
            ->where('user_id', $request->user()->id)
            ->where('location_id', $location->id)
            ->exists();

        if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'upvotes_count' => $upvotesCount,
                'has_voted' => $hasVoted,
            ]);
        }

        return back()->with('ok', $hasVoted ? 'Vote enregistré.' : 'Vote retiré.');
    }

    public function edit(Request $request, Location $location): View
    {
        if (! $this->userCanManage($request->user(), $location)) {
            abort(403);
        }

        $films = Film::query()->orderBy('title')->get();

        return view('locations.edit', compact('location', 'films'));
    }

    public function update(Request $request, Location $location): RedirectResponse
    {
        if (! $this->userCanManage($request->user(), $location)) {
            abort(403);
        }

        $data = $request->validate([
            'film_id' => ['required', 'exists:films,id'],
            'name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:4096'],
        ]);

        if ($request->hasFile('photo')) {
            if ($location->photo_path) {
                Storage::disk('public')->delete($location->photo_path);
            }

            $data['photo_path'] = $request->file('photo')->store('locations', 'public');
        }

        unset($data['photo']);

        $location->update($data);

        return redirect()->route('locations.show', $location)->with('ok', 'Emplacement mis à jour.');
    }

    public function destroy(Request $request, Location $location): RedirectResponse
    {
        if (! $this->userCanManage($request->user(), $location)) {
            abort(403);
        }

        if ($location->photo_path) {
            Storage::disk('public')->delete($location->photo_path);
        }

        $location->delete();

        return redirect()->route('locations.index')->with('ok', 'Emplacement supprimé.');
    }

    private function userCanManage(?User $user, Location $location): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->is_admin) {
            return true;
        }

        return (int) $user->id === (int) $location->user_id;
    }
}
