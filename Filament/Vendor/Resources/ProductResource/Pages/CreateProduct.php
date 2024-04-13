<?php

namespace App\Filament\Vendor\Resources\ProductResource\Pages;

use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Vendor\Resources\ProductResource;
use App\Models\StoreProduct;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    /*     protected function handleRecordCreation(array $data): Model
    {
        //insert the student
        $record =  static::getModel()::create($data);

        // Create a new Guardian model instance
        $guardian = new StoreProduct();
        $guardian->first_name = $data['guardian_fname'];
        $guardian->last_name = $data['guardian_lname'];
        $guardian->gender = $data['guardian_gender'];
        $guardian->email = $data['guardian_email'];
        $guardian->contact_no = $data['guardian_contact'];

        // Assuming 'student_id' is the foreign key linking to students
        $guardian->student_id = $record->student_id;

        // Save the Guardian model to insert the data
        $guardian->save();


        return $record;
    } */
}
