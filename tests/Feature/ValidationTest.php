<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ValidationTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testCompanySymbolMissing()
    {
        $response = $this->post('/getStockData', []);
        $response->assertStatus(200)
                ->assertJsonValidationErrors('companySymbol');
    }

    public function testValidatesCompanySymbol()
    {
        $validCompanySymbol = ['ABC123', 'XYZ456', '123DEF'];
        $invalidCompanySymbol = 'INVALID';

        foreach ($validCompanySymbol as $key => $companySymbol) {
            $response = $this->post('/getStockData', [
                'companySymbol' => $key,
            ]);

            $response->assertStatus(200);
        }

        $response = $this->post('/getStockData', [
            'companySymbol' => $invalidCompanySymbol,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('companySymbol');
    }    

    public function testStartDateMissing()
    {
        $response = $this->post('/getStockData', []);
        $response->assertStatus(200) 
                ->assertJsonValidationErrors('startDate');

    }

    public function testValidatesStartDateNotMoreThanToday()
    {
        $futureDate = Carbon::now()->addDays(1)->toDateString(); // A future date
        $today = Carbon::now()->toDateString(); // Today's date

        $response = $this->post('/getStockData', [
            'startDate' => $futureDate, // Provide a future date
        ]);

        $response->assertStatus(422); // Expect a 422 status for validation error
        $response->assertJsonValidationErrors(['startDate']);

        $response = $this->post('/getStockData', [
            'startDate' => $today, // Provide today's date
        ]);

        $response->assertStatus(200); // Expect a 200 status for successful submission
    }

    public function testEndDateMissing()
    {
        $response = $this->post('/getStockData', []);
        $response->assertStatus(200) 
                ->assertJsonValidationErrors('endDate');
    }

    public function testValidatesEndDateNotMoreThanToday()
    {
        $futureDate = Carbon::now()->addDays(1)->toDateString(); // A future date
        $today = Carbon::now()->toDateString(); // Today's date

        $response = $this->post('/getStockData', [
            'endDate' => $futureDate, // Provide a future date
        ]);

        $response->assertStatus(422); // Expect a 422 status for validation error
        $response->assertJsonValidationErrors(['endDate']);

        $response = $this->post('/getStockData', [
            'endDate' => $today, // Provide today's date
        ]);

        $response->assertStatus(200); // Expect a 200 status for successful submission
    }

    public function testValidatesEndDateNotLessThanStartSate()
    {
        $startDate = '2023-10-15';
        $endDate = '2023-10-20'; // A date greater than start_date

        $response = $this->post('/getStockData', [
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);

        $response->assertStatus(200); // Expect a 200 status for a valid date range

        $pastEndDate = '2023-10-10'; // A date less than start_date

        $response = $this->post('/getStockData', [
            'startDate' => $startDate,
            'endDate' => $pastEndDate,
        ]);

        $response->assertStatus(422); // Expect a 422 status for a validation error
        $response->assertJsonValidationErrors(['end_date']);
    }

    public function testEmailMissing()
    {
        $response = $this->post('/getStockData', []);
        $response->assertStatus(200) 
                ->assertJsonValidationErrors('email');
    }

    public function testValidatesEmail()
    {
        $validEmail = 'valid.email@example.com'; // A valid email address

        $response = $this->post('/getStockData', [
            'email' => $validEmail,
        ]);

        $response->assertStatus(200); // Expect a 200 status for a valid email address

        $invalidEmail = 'invalid-email'; // An invalid email address

        $response = $this->post('/getStockData', [
            'email' => $invalidEmail,
        ]);

        $response->assertStatus(422); // Expect a 422 status for a validation error
        $response->assertJsonValidationErrors(['email']);
    }
}
