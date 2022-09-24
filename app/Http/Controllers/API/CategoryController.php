<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiHandler;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    use ApiHandler;
    public function index()
    {
        $categories = Category::select("id","name_".$this->getCurrentLang())->get();
        return $this->returnData("Categories",$categories);
    }

    public function show(Request $request)
    {
        $category = Category::select('id','name_'.$this->getCurrentLang())->find($request->categ_id);
        if (!$category) {
            return $this->returnError("","SomeThing Went Wrong !");
        }
        return $this->returnData("Category",$category);
    }


    public function store(Request $request)
    {
        try {
            $rules = [
                'name_ar' => 'required|string',
                'name_en' => 'required|string',
            ];
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($validator, $code);
            }

            $category = Category::create([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
            ]);
            if (!$category) {
                return $this->returnError("", "SomeThing Went Wrong !");
            }

            return $this->returnSuccessMessage("", "Category Has been Added Successfully");
        } catch (\Exception $e) {
            return $this->returnError("", "SomeThing Went Wrong !");
        }
    }




    public function update(Request $request, Category $product)
    {
        try {
            $rules = [
                'name_ar' => 'required|string',
                'name_en' => 'required|string',
            ];
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($validator, $code);
            }
            $updated = Category::where('id', $request->categ_id)
                ->update([
                    'name_ar' => $request->name_ar,
                    'name_en' => $request->name_en,
                ]);

            if (!$updated) {
                return $this->returnError("", "SomeThing Went Wrong !");
            }

            return $this->returnSuccessMessage("", "Category Has been Updated Successfully");
        } catch (\Exception $e) {
            return $this->returnError("", "SomeThing Went Wrong !");
        }
    }

    public function destroy(Request $request)
    {
        $category = Category::find($request->categ_id);
        if ($category) {
            $category->delete();
            return $this->returnSuccessMessage("Category Has been deleted successfully");
        }
        return $this->returnError("","some thing went wrong !");
    }
}
