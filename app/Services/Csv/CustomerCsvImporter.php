<?php

namespace App\Services\Csv;

use App\Models\Customer;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Support\CrmValidation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;

class CustomerCsvImporter
{
    /** @var list<string> */
    public const HEADERS = [
        'name',
        'email',
        'phone',
        'company_name',
        'address',
        'notes',
    ];

    public function __construct(
        protected CsvReader $reader,
    ) {}

    public function import(User $actor, UploadedFile $file): CsvImportResult
    {
        $parsed = $this->reader->read($file);
        $result = new CsvImportResult;
        $seenEmails = [];

        foreach ($parsed['rows'] as $row) {
            $rowNumber = $row['row'];
            $data = $row['data'];

            $payload = [
                'name' => $data['name'] ?? '',
                'email' => ($data['email'] ?? '') !== '' ? $data['email'] : null,
                'phone' => ($data['phone'] ?? '') !== '' ? $data['phone'] : null,
                'company_name' => ($data['company_name'] ?? '') !== '' ? $data['company_name'] : null,
                'address' => ($data['address'] ?? '') !== '' ? $data['address'] : null,
                'notes' => ($data['notes'] ?? '') !== '' ? $data['notes'] : null,
            ];

            $validator = Validator::make($payload, CrmValidation::customerStoreRules(forImport: true));

            if ($validator->fails()) {
                $result->addError($rowNumber, $validator->errors()->first());

                continue;
            }

            $validated = $validator->validated();
            $email = isset($validated['email']) ? mb_strtolower(trim((string) $validated['email'])) : null;

            if ($email !== null && $email !== '') {
                if (isset($seenEmails[$email])) {
                    $result->addDuplicate($rowNumber, "Duplicate email in file: {$email}");

                    continue;
                }

                if (Customer::query()->whereRaw('LOWER(email) = ?', [$email])->exists()) {
                    $result->addDuplicate($rowNumber, "Customer with email already exists: {$email}");

                    continue;
                }

                $seenEmails[$email] = true;
            }

            $customer = Customer::create([
                'created_by' => $actor->id,
                'name' => $validated['name'],
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'company_name' => $validated['company_name'] ?? null,
                'address' => $validated['address'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => 'active',
            ]);

            ActivityLogger::log('customer.created', $customer, [
                'name' => $customer->name,
                'via' => 'csv_import',
            ]);

            $result->imported++;
        }

        return $result;
    }
}
