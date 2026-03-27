<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Service::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                    ->orWhere('categorie', 'like', "%{$search}%");
            });
        }

        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }

        return Inertia::render('Services/Index', [
            'services' => $query->latest()->paginate(30)->withQueryString(),
            'filters' => $request->only(['search', 'categorie']),
            'categories' => Service::distinct('categorie')->pluck('categorie')->filter()->values(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unite' => 'nullable|string|max:50',
            'prix_indicatif' => 'nullable|numeric|min:0',
            'categorie' => 'nullable|string|max:100',
        ]);

        Service::create($validated);

        return redirect()->route('services.index')->with('success', 'Service ajouté.');
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unite' => 'nullable|string|max:50',
            'prix_indicatif' => 'nullable|numeric|min:0',
            'categorie' => 'nullable|string|max:100',
        ]);

        $service->update($validated);

        return redirect()->route('services.index')->with('success', 'Service modifié.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        $service->delete();

        return redirect()->route('services.index')->with('success', 'Service supprimé.');
    }
}
