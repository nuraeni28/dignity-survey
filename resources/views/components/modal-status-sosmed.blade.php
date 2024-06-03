<div class="modal fade" id="N{{ $volunteer->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>
            <form
                action="{{ route('relawan-sosmed.getStatus', ['userId' => $volunteer->id, 'page' => $volunteers->currentPage()]) }}"
                method="POST">
                @csrf
                <div class="modal-body" style="max-height: 500px; overflow-y: auto;">
                    <input type="hidden" id="id" name="id">
                    <div class="row">
                        <div class="col-12 mt-4">
                            <select class="form-select" aria-label="Default select example" name="status"
                                id="status" required>
                                <option value="" disabled selected>Pilih Status</option>
                                <option value="Aktif"{{ $volunteer->status === 'Aktif' ? ' selected' : '' }}>Aktif
                                </option>
                                <option value="Non-Aktif"{{ $volunteer->status === 'Non-Aktif' ? ' selected' : '' }}>
                                    Non-Aktif</option>
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
<div class="modal fade" id="updateAll" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>
            <form action="{{ route('relawan-sosmed.getStatusAll') }}" method="POST" id="updateAllSelectedRecordForm">
                @csrf
                <div class="modal-body" style="max-height: 500px; overflow-y: auto;">
                    <input type="hidden" id="id" name="id">
                    <div class="row">
                        <div class="col-12 mt-4">
                            <select class="form-select" aria-label="Default select example" name="status"
                                id="status" required>
                                <option value="" disabled selected>Pilih Status</option>
                                <option value="Aktif"{{ $volunteer->status === 'Aktif' ? ' selected' : '' }}>Aktif
                                </option>
                                <option value="Non-Aktif"{{ $volunteer->status === 'Non-Aktif' ? ' selected' : '' }}>
                                    Non-Aktif</option>
                            </select>
                            @if ($errors->has('status'))
                                <p class="text-danger">{{ $errors->first('status') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="buttonStatus">Save</button>
                </div>
        </div>
        </form>
    </div>
</div>

<div class="modal fade" id="AllSosmed" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>
            @php
                $cities = new App\Http\Controllers\DependentDropdownController();
                $cities = $cities->citiesData(27);
            @endphp
            <form action="{{ route('relawan-sosmed.getStatusKabupaten') }}" method="POST">
                @csrf
                <div class="modal-body" style="max-height: 500px; overflow-y: auto;">
                    {{-- <input type="hidden" id="id" name="id"> --}}
                    <div class="row">
                        <div class="col-12 mt-4">
                            <select name="indonesia_cities_id" class="form-select form-select-sm" id="city">
                                <option value="">Kabupaten</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('indonesia_cities_id'))
                                <p class="text-danger">{{ $errors->first('indonesia_cities_id') }}</p>
                            @endif
                        </div>
                        <div class="col-12 mt-4">
                            <select class="form-select" aria-label="Default select example" name="status"
                                id="status" required>
                                <option value="" disabled selected>Pilih Status</option>
                                <option value="Aktif">Aktif
                                </option>
                                <option value="Non-Aktif">
                                    Non-Aktif</option>
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
