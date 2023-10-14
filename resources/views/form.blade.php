@extends('welcome')

@section('content')
    
    <div class="loader-container" id="loader" style="display:none">
        <div class="loader"></div>
    </div>

    <div class="alert alert-success d-none" id="successMsg"></div>
    <div class="alert alert-danger d-none" id="errorMsg"></div>

    <form id="companyForm">
        @csrf
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label" class="required-label">Company Symbol*</label>
                <input type="text" class="form-control" name="companySymbol" id="companySymbol" placeholder="ABCD" maxlength="10">
                <div class="error" id="companySymbol_error"></div>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Start Date*</label>
                <input type="text" class="form-control" name="startDate" id="startDate" placeholder="YYYY-MM-DD" readonly>
                <div class="error" id="startDate_error"></div>
            </div>
        </div> 
        <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">End Date*</label>
            <input type="text" class="form-control" name="endDate" id="endDate" placeholder="YYYY-MM-DD" readonly>
            <div class="error" id="endDate_error"></div>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Email*</label>
            <input type="email" class="form-control" name="email" id="email" placeholder="name@example.com">
            <div class="error" id="email_error"></div>
        </div>
    </div>
    </form>
    <div class="mb-3">
        <button class="btn btn-info" onClick="submitForm()">Submit</button>
    </div>
    
    <div class="table-container mb-5">
        <table id="historicDataTable"  class="table table-bordered">
            <thead>
                <tr id="historicDataHead">
                    <th>Date</th>
                    <th>Open</th>
                    <th>High</th>
                    <th>Low</th>
                    <th>Close</th>
                    <th>Volume</th>
                </tr>
            </thead>
            <tbody id="historicData">
                <td colspan="6">No data to display</td>
            </tbody>    
        </table>
    </div>

 
    <div id="chartContainer" style="height: 370px; width: 100%;"></div>

<script>
    const symbolList = @json(config('masterlist.companySymbolsMaster'));    
</script>
@endsection