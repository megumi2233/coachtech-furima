<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;   
use App\Models\Condition;  
use App\Http\Requests\ExhibitionRequest; 
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    
    public function index(Request $request)
    {
        $query = Item::query();

        
        
        if (Auth::check()) {
            $query->where('user_id', '!=', Auth::id());
        }
        

        
        if ($request->query('tab') === 'mylist') {

            
            if (!Auth::check()) {
                return view('index', ['items' => []]);
            }

            
            $user_id = Auth::id();
            $query->whereHas('likes', function ($q) use ($user_id) {
                $q->where('user_id', $user_id);
            });
        }

        
        if ($keyword = $request->query('keyword')) {
            $query->where('name', 'LIKE', "%{$keyword}%");
        }

        
        $items = $query->orderBy('id', 'desc')->get();

        return view('index', compact('items'));
    }

    
    public function show($item_id)
    {
        $item = Item::findOrFail($item_id);
        return view('item.show', compact('item'));
    }

    
    public function create()
    {
        
        $categories = Category::all();
        $conditions = Condition::all();

        
        return view('item.create', compact('categories', 'conditions'));
    }

    
    public function store(ExhibitionRequest $request)
    {
        
        
        $imagePath = $request->file('image')->store('item_images', 'public');

        
        
        $item = Item::create([
            'user_id'     => Auth::id(),
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'img_url'     => $imagePath,
            'brand_name'  => $request->brand,
            'condition_id' => $request->condition_id,
        ]);

        
        
        $item->categories()->sync($request->categories);

        
        return redirect()->route('root');
    }
}
