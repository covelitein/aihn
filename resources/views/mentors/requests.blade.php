<x-app-layout>
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col">
                <h4 class="mb-0 fw-bold">Find a Mentor</h4>
                <p class="text-muted mb-0">Send a request to a mentor to be assigned.</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="row g-3">
                    @forelse($mentorsArray as $mentor)
                        <div class="col-md-4">
                            <div class="p-3 border rounded d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold">{{ $mentor->name }}</div>
                                    <div class="text-muted small">{{ $mentor->email }}</div>
                                </div>
                                <div>
                                    @php($req = $existing[$mentor->id] ?? null)
                                    @if($req)
                                        <span class="badge bg-{{ $req->status === 'accepted' ? 'success' : ($req->status === 'rejected' ? 'secondary' : 'warning text-dark') }}">
                                            {{ ucfirst($req->status) }}
                                        </span>
                                    @else
                                        <form action="{{ route('mentors.request') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="mentor_id" value="{{ $mentor->id }}">
                                            <button class="btn btn-primary btn-sm">Request</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center text-muted py-4">No mentors available.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


