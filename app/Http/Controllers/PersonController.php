<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePersonRequest;
use App\Http\Requests\UpdatePersonRequest;
use App\Models\Person;

class PersonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Person::query();
        if (request()->name) {
            $name = request()->name;
            $query->where('name', 'like', "%$name%");
        }

        return $query->latest()->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePersonRequest $request)
    {
        try {
            $person = Person::create($request->all());
            $person->contacts()->createMany([...$request->contacts]);
            return ['data' => [$person, $person->contacts]];
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return ['data' => Person::with('contacts')->findOrFail($id)];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePersonRequest $request, $id)
    {
        try {

            $person = Person::findOrFail($id);
            $person->fill($request->all())->save();

            // delete contacts that are not present in the request
            $idsUpdate = collect($request->contacts)->pluck('id')->whereNotNull()->toArray();
            $person->contacts
                ->whereNotIn('id', $idsUpdate)
                ->each(fn($contact) => $contact->delete());

            // update or create contacts
            foreach ($request->contacts as $contact) {
                if (isset($contact['id'])) {
                    $person->contacts->findOrFail($contact['id'])->update($contact);
                } else {
                    $person->contacts()->create($contact)->save();
                }
            }

            return ['data' => $person->fresh('contacts')];
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $person = Person::findOrFail($id);
            $person->contacts->each(fn($contact) => $contact->delete());
            $person->delete();

            return response()->json(['message' => 'Contact deleted successfully'], 200);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }
}
