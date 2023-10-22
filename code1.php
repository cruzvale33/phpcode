<?php

 // update record in the database
 public function update(array $data, $id, array $relations = [])
 {
     return $this->show(DB::transaction(function () use ($data, $id) {
         if (in_array('created_by', $this->getModel()->getFillable())) {
             $data['updated_by'] = Auth::id() ?? null;
         }

         $model = $this->model->loadMissing(['solutionManagment', 'productManagment', 'voucher.client'])->find($id);

         if (! empty($data['solution_managment_id']) || ! empty($data['price'])) {
             $model->price_client = $this->getClientprice($data['solution_managment_id'] ?? $model->solution_managment_id, $model->voucher_id, $data['price'] ?? $model->price, $data['solution_parent_id'] ?? $model->solution_parent_id);
         }

         $this->assignSupervisor($data, $model);
         //$this->createJiraTask($data, $model);
         if (! empty($data['price'])) {
             $data['iva'] = $this->setIva($model->voucher_id, $model->price);
         }

         if (! empty($data['onboarding'])) {
             $data['onboarding'] = $this->setOnboarding($model, $data['onboarding']);
         }

         $model->update($data);

         $this->updateVoucher($model->voucher_id);
         $this->checkSolutionStatusByVoucherId($model->voucher);

         return $model->id;
     }), null);
 }

 public function setOnboarding(Solution $solution, $onboardingData)
 {
     if (empty($onboardingData['updateOnboarding'])) {

         $usersFlesip = null;
         $company = [
             'email' => $solution->voucher->client->email,
             'name' => $solution->voucher->client->name,
             'role' => 'company',
             'is_customer' => 1,
         ];

         $this->createUserSupport($solution->voucher->client->email, $solution->voucher->client->name, $solution, $solution->voucher->client->email);

         if (isset($onboardingData['licenses']) && ! empty($onboardingData['licenses'])) {
             foreach ($onboardingData['licenses'] as $key => $license) {

                 $onboardingData['licenses'][$key]['img'] = $this->uploadOnboardingAttach($license, $solution);

                 if ($onboardingData['isFlesip']) {
                     if ($key == 0) {
                         $company['document'] = $license['licenseNumber'] ?? null;
                         $company['email'] = $license['email'];
                     }
                     $usersFlesip[$key] = [
                         'email' => $license['email'],
                         'name' => $license['licenseNumber'],
                         'document' => $license['licenseNumber'],
                         'printerSerial' => $license['printerSerial'],
                         'role' => 'user',
                         'is_customer' => 0,
                     ];
                 }

                 $this->createUserSupport($license['email'], $license['licenseNumber'], $solution, $solution->voucher->client->email);

             }
         }

         $onboardingData['isFlesip'] ? $this->createUsersFlesip($company, $usersFlesip) : null;

         $this->sendOnboardingNotif($onboardingData, $solution);

     }

     return $onboardingData;
 }

 protected function createUsersFlesip($company, $users)
 {

     $data = [
         'data' => array_merge([
             'type' => 'companies',
             'attributes' => [
                 'name' => $company['name'],
                 'userData' => [
                     'name' => $company['name'],
                     'email' => $company['email'],
                     'document' => $company['document'] ?? null,
                 ],
                 'companyUsers' => ! empty($users) ? $users : [],
             ],

         ],
             ! empty($users) ? [
                 'relationships' => [
                     'profile' => [
                         'data' => [
                             'type' => 'profiles',
                             'id' => '1',
                         ],
                     ],
                 ],
             ] : [],
         ),
     ];

     $response = Http::withHeaders([
         'Authorization' => config('app.flesip.SECURE_KEY'),
         'Accept' => 'application/vnd.api+json',
         'Content-Type' => 'application/vnd.api+json',
     ])
         ->post(
             config('app.flesip.URL').'/external/companies',
             $data
         );

     return $response->successful() ? true : abort('403', $response->body());

 }

 protected function createUserSupport($email, $name, Solution $solution, $customerAlias = null)
 {
     $data = [
         'customerAlias' => $customerAlias,
         'name' => $name,
         'email' => $email,
         'token' => config('app.support.SECURE_KEY'),
         'productsAlias' => [
             $solution->solutionManagment->jira_alias,
         ],
     ];

     $response = Http::withHeaders([
         'Authorization' => config('app.support.SECURE_KEY'),
     ])
         ->post(
             config('app.support.URL').'/v1/external/login',
             $data
         );

     return $response->successful() ? true : abort('403', $response->body());
 }

 protected function uploadOnboardingAttach($license, Solution $solution)
 {
     if (! empty($license['img']) && str_contains($license['img'], 'tmp/')) {

         $path = str_replace('tmp/', "Solutions/{$solution->id}/", $license['img']);

         if (! Storage::copy(
             $license['img'],
             $path
         )) {
             abort(500, 'Error copiando el archivo al destino.');
         }

         return $path;
     }
 }
