<?php

namespace App\Http\Controllers;

use App\Models\Film;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FilmController extends Controller
{
    public function index(): View
    {
        $films = Film::query()->withCount('locations')->orderBy('title')->get();

        return view('films.index', compact('films'));
    }

    public function create(): View
    {
        return view('films.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'release_year' => ['required', 'integer', 'min:1888', 'max:'.(date('Y') + 5)],
            'synopsis' => ['nullable', 'string'],
        ]);

        Film::query()->create($data);

        return redirect()->route('films.index')->with('ok', 'Film créé.');
    }

    public function show(Request $request, Film $film): View
    {
        $city = trim((string) $request->query('city', ''));
        $country = trim((string) $request->query('country', ''));

        $film->load([
            'locations' => fn ($query) => $query
                ->when($city !== '', fn ($q) => $q->where('city', 'like', '%'.$city.'%'))
                ->when($country !== '', fn ($q) => $q->where('country', 'like', '%'.$country.'%'))
                ->orderBy('name')
                ->with('user'),
        ]);

        return view('films.show', [
            'film' => $film,
            'filters' => [
                'city' => $city,
                'country' => $country,
            ],
        ]);
    }

    public function edit(Film $film): View
    {
        return view('films.edit', compact('film'));
    }

    public function update(Request $request, Film $film): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'release_year' => ['required', 'integer', 'min:1888', 'max:'.(date('Y') + 5)],
            'synopsis' => ['nullable', 'string'],
        ]);

        $film->update($data);

        return redirect()->route('films.show', $film)->with('ok', 'Film mis à jour.');
    }

    public function destroy(Film $film): RedirectResponse
    {
        $film->delete();

        return redirect()->route('films.index')->with('ok', 'Film supprimé.');
    }
}
