<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttributeController extends Controller
{
    public function index()
    {
        $attributes = Attribute::with('values')->get();

        return response()->json([
            'success' => true,
            'data' => $attributes
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'values' => 'nullable|array'
        ]);

        $code = Str::slug($request->name);

        $attribute = Attribute::firstOrCreate(
            ['code' => $code],
            ['name' => $request->name]
        );

        if ($request->filled('values') && is_array($request->values)) {
            foreach ($request->values as $val) {
                if (!empty($val)) {
                    AttributeValue::firstOrCreate([
                        'attribute_id' => $attribute->id,
                        'value' => trim($val)
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Attribute '{$attribute->name}' saved to successfully!",
            'data' => $attribute->load('values')
        ]);
    }

    public function addValue(Request $request, $id)
    {
        $request->validate([
            'value' => 'required|string|max:255'
        ]);

        $attribute = Attribute::findOrFail($id);

        $value = AttributeValue::firstOrCreate([
            'attribute_id' => $attribute->id,
            'value' => trim($request->value)
        ]);

        return response()->json([
            'success' => true,
            'message' => "Value '{$value->value}' added to attribute '{$attribute->name}' in database!",
            'data' => $value
        ]);
    }

    public function destroy($id)
    {
        $attribute = Attribute::findOrFail($id);
        $name = $attribute->name;
        $attribute->delete();

        return response()->json([
            'success' => true,
            'message' => "Attribute '{$name}' deleted from database!"
        ]);
    }

    public function destroyValue($id)
    {
        $val = AttributeValue::findOrFail($id);
        $text = $val->value;
        $val->delete();

        return response()->json([
            'success' => true,
            'message' => "Value '{$text}' deleted from database!"
        ]);
    }
}
