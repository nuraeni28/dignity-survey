<div class="modal fade" id="N{{ $customer->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>
            <form
                action="{{ route('customer.getStatusCustomer', ['id' => $customer->id, 'page' => $customers->currentPage()]) }}"
                method="POST">
                @csrf
                <div class="modal-body" style="max-height: 500px; overflow-y: auto;">
                    <input type="hidden" id="id" name="id">
                    <div class="row">
                        <div class="col-12 mt-4">
                            <select class="form-select" aria-label="Default select example" name="status"
                                id="status" required>
                                <option value="" disabled selected>Pilih Status</option>
                                <option value="Verified"{{ $customer->status === 'Verified' ? ' selected' : '' }}>
                                    Verified
                                </option>
                                <option value="Unverified"{{ $customer->status === 'Unverified' ? ' selected' : '' }}>
                                    Unverified</option>
                            </select>
                            @if ($errors->has('status'))
                                <p class="text-danger">{{ $errors->first('status') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
        </div>
        </form>
    </div>
</div>
