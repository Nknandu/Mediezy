<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\Medicalshop;
use App\Models\MedicineProduct;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
class MedicalshopController extends BaseController
{


    public function MedicalshopRegister(Request $request)
    {
        try {
            DB::beginTransaction();

            $input = $request->all();
            $validator = Validator::make($input, [
                'firstname' => 'required',
                'email' => 'required',
                'password' => 'required',
                'mobileNo'=> 'required',
                'address'=> 'required',

            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors());
            }

            $emailExists = Medicalshop::where('email', $input['email'])->count();
            $emailExistsinUser = User::where('email', $input['email'])->count();

            if ($emailExists && $emailExistsinUser) {
                return $this->sendResponse("Laboratory", null, '3', 'Email already exists.');
            }

            $input['password'] = Hash::make($input['password']);

            $userId = DB::table('users')->insertGetId([
                'firstname' => $input['firstname'],
                'secondname' => 'Medicalshop',
                'email' => $input['email'],
                'password' => $input['password'],
                'mobileNo' => $input['mobileNo'],
                'user_role' => 5,
            ]);

            $DocterData = [

                'firstname' => $input['firstname'],
                'mobileNo' => $input['mobileNo'],
                'email' => $input['email'],
                'location' => $input['location'],
                'address' => $input['address'],
                'UserId' => $userId,
            ];

            if ($request->hasFile('shop_image')) {
                $imageFile = $request->file('shop_image');

                if ($imageFile->isValid()) {
                    $imageName = $imageFile->getClientOriginalName();
                    $imageFile->move(public_path('shopImages/images'), $imageName);

                    $DocterData['shop_image'] = $imageName;
                }
            }

            $Medicalshop = new Medicalshop($DocterData);
            $Medicalshop->save();
            DB::commit();

            return $this->sendResponse("Medicalshop", $Medicalshop, '1', 'Medicalshop created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), $errorMessages = [], $code = 404);
        }
    }



    public function MedicineProduct(Request $request)
    {
        try {
            // Validate request data
            $this->validate($request, [
                'medicalshop_id' => 'required',
                'MedicineName' => 'required',
                'product_description' => 'sometimes',
                'product_price' => 'required',
                'discount' => 'sometimes',
            ]);

            // Extract data from the request
            $medicalshop_id = $request->input('medicalshop_id');
            $MedicineName = $request->input('MedicineName');
            $product_description = $request->input('product_description');

            $discount = $request->input('discount');
            $product_price = str_replace(',', '', $request->input('product_price'));
            $product_price = floatval($product_price);

            // Check if discount is provided and is numeric
            if ($discount !== null && is_numeric($discount)) {
                $Total_price = $product_price - ($product_price * $discount / 100);
            } else {
                $Total_price = $product_price;
            }

            $MedicineData = [
                'medicalshop_id' => $medicalshop_id,
                'MedicineName' => $MedicineName,
                'product_description' => $product_description,
                'product_price' => $product_price,
                'discount' => $discount,
                'Total_price' => $Total_price,
            ];

            // Upload and save the image if provided
            if ($request->hasFile('product_image')) {
                $imageFile = $request->file('product_image');

                if ($imageFile->isValid()) {
                    $imageName = $imageFile->getClientOriginalName();
                    $imageFile->move(public_path('shopImages/medicine'), $imageName);

                    $MedicineData['product_image'] = $imageName;
                }
            }

            // Save the data to the database
            $Medicine = new MedicineProduct($MedicineData);
            $Medicine->save();

            // Return success response
            return $this->sendResponse('MedicineProduct', $MedicineData, '1', 'Medicine added successfully.');

        } catch (ValidationException $e) {
            // Handle validation errors
            return $this->sendError('Validation Error', $e->errors(), 422);
        } catch (QueryException $e) {
            // Handle database query errors
            return $this->sendError('Database Error', $e->getMessage(), 500);
        } catch (\Exception $e) {
            // Handle other unexpected errors
            return $this->sendError('Error', $e->getMessage(), 500);
        }
    }


}
