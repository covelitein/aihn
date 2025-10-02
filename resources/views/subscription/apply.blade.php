@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Subscribe to {{ $plan->name }}</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('subscription.plans') }}" class="btn btn-light btn-sm">
                            <i class="bi bi-arrow-left me-1"></i> Back to Plans
                        </a>
                    </div>
                    <span class="badge bg-light text-primary">${{ number_format($plan->price, 2) }}</span>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>There were some problems with your submission:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('subscription.submit', $plan) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="amount_paid" class="form-label">Amount Paid</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" 
                                       class="form-control @error('amount_paid') is-invalid @enderror" 
                                       id="amount_paid" name="amount_paid" 
                                       value="{{ old('amount_paid', $plan->price) }}"
                                       required>
                                @error('amount_paid')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="transaction_id" class="form-label">Transaction ID</label>
                            <input type="text" class="form-control @error('transaction_id') is-invalid @enderror" 
                                   id="transaction_id" name="transaction_id" 
                                   value="{{ old('transaction_id') }}"
                                   placeholder="Optional - Enter your payment transaction ID">
                            @error('transaction_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <select id="payment_method" name="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                                <option value="" disabled {{ old('payment_method') ? '' : 'selected' }}>Select a method</option>
                                <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="credit_card" {{ old('payment_method') === 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                <option value="paypal" {{ old('payment_method') === 'paypal' ? 'selected' : '' }}>PayPal</option>
                                <option value="other" {{ old('payment_method') === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="proof_of_payment" class="form-label">Proof of Payment</label>
                            <input type="file" class="form-control @error('proof_of_payment') is-invalid @enderror" 
                                   id="proof_of_payment" name="proof_of_payment" required>
                            <div class="form-text">
                                Upload receipt or screenshot of payment (JPG, PNG, or PDF, max 5MB)
                            </div>
                            @error('proof_of_payment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <h5 class="mb-3">Contact Information</h5>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" 
                                   value="{{ old('phone', auth()->user()->profile->phone ?? '') }}"
                                   required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="company_name" class="form-label">Company Name</label>
                            <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                   id="company_name" name="company_name" 
                                   value="{{ old('company_name', auth()->user()->profile->company_name ?? '') }}"
                                   placeholder="Optional">
                            @error('company_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" name="address" rows="3" required>{{ old('address', auth()->user()->profile->address ?? '') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
                                <i class="bi bi-arrow-left me-1"></i> Back
                            </button>
                            <button type="submit" class="btn btn-primary">
                                Submit Application
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection