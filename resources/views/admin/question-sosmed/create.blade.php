@extends('adminlte::page')

@section('title', 'Create Question Relawan Sosmed')

@section('content_header')
    <h1>Create Question Relawan Sosmed</h1>
@stop

@section('content')
    <form action="{{ route('question-sosmed.store') }}" method="post">
        @csrf
        <x-adminlte-input name="question" value="{{ old('question') }}" required label="Pertanyaan" placeholder="pertanyaan"
            type="text" igroup-size="md" />
        <x-adminlte-select id="type" required value="{{ old('type') }}" name="type" label="Tipe">
            <option value="Essai">Essai</option>
            <option value="Option">Pilihan</option>
            <option value="Multiple">Multiple</option>
        </x-adminlte-select>
        <div id="option_field" class="py-2" style="display: none">
            <table class="table table-borderless" id="option"></table>
            <x-adminlte-button id="option_button" type="button" label="Tambah Opsi" theme="success"
                icon="fas fa-lg fa-plus" />
        </div>
        <x-adminlte-button class="btn-flat" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save" />
    </form>
@stop

@section('js')
    <script>
        var i = 2;
        $(document).ready(function() {
            $('#type').change(function() {
                if (this.value == 'Option' || this.value == 'Multiple') {
                    $('#option').children('tr').remove();
                    $('#option_field').fadeIn('slow');
                    $("#option").append(`
                    <tr>
                        <td>
                            <x-adminlte-input id="1" name="option[]" value="{{ old('option[]') }}" label="Opsi" placeholder="Opsi" type="text" igroup-size="md" />
                        </td>
                        <td>
                          <button onclick="trigger(1)" type='button' class="btn btn-xs btn-default text-success mx-1 shadow btn-add" title="Tambah">
                            <i class="fa fa-lg fa-fw fa-plus"></i>
                            Jawaban
                          </button>
                        </td>
                    </tr>
                    <tr id="btn1"></tr>
                    <tr>
                        <td>
                            <x-adminlte-input id="2" name="option[]" value="{{ old('option[]') }}" label="Opsi" placeholder="Opsi" type="text" igroup-size="md" />
                        </td>
                        <td>
                          <button onclick="trigger(2)" type='button' class="btn btn-xs btn-default text-success mx-1 shadow btn-add" title="Tambah">
                            <i class="fa fa-lg fa-fw fa-plus"></i>
                            Jawaban
                          </button>
                        </td>
                    </tr>
                    <tr id="btn2"></tr>
                    `);
                } else {
                    $('#option_field').fadeOut('slow');
                    $('#option').children('tr').remove();
                }
            });
            $("#option_button").click(function() {
                ++i;
                $("#option").append(`
                    <tr>
                        <td>
                            <x-adminlte-input id="${i}" name="option[]" value="{{ old('option[]') }}" label="Opsi" placeholder="Opsi" type="text" igroup-size="md" />
                        </td>
                        <td>
                            <button onclick="trigger(${i})" type='button' class="btn btn-xs btn-default text-success mx-1 shadow btn-add" title="Tambah">
                              <i class="fa fa-lg fa-fw fa-plus"></i>
                              Jawaban
                            </button>
                            <button type="button" class="btn btn-xs btn-default text-danger mx-1 shadow remove-input-field" title="Delete">
                              <i class="fa fa-lg fa-fw fa-trash"></i>
                              Delete
                            </button>
                        </td>
                    </tr>
                    <tr id="btn${i}"></tr>
                    `);
            });
            $(document).on('click', '.remove-input-field', function() {
                --i;
                $('#option_button').fadeIn('slow');
                $(this).parents('tr').remove();
            });
        });

        function trigger(id) {
            let name = id;
            $(`#btn${id}`).append(`
      <td>
        <x-adminlte-input name="${name}_question" label="Pertanyaan" placeholder="pertanyaan" type="text" igroup-size="md" />
      </td>
      <td>
        <x-adminlte-select id="type${id}" name="${name}_type" label="Tipe">
          <option value="Essai">Essai</option>
          <option value="Option">Pilihan</option>
          <option value="Multiple">Multiple</option>
        </x-adminlte-select>
      </td>
      <td>
        <div id="option_field${id}" class="py-2" style="display: none">
          <table class="table table-borderless" id="option${id}"></table>
          <x-adminlte-button id="option_button${id}" type="button" label="Tambah Opsi" theme="success" icon="fas fa-lg fa-plus" />
        </div>
      </td>
    `);
            $(`#type${id}`).change(function() {
                if (this.value == 'Option' || this.value == 'Multiple') {
                    ++i;
                    $(`#option${id}`).children('tr').remove();
                    $(`#option_field${id}`).fadeIn('slow');
                    $(`#option${id}`).append(`
                    <tr>
                        <td>
                            <x-adminlte-input id="${i}" name="${name}_option[]" label="Opsi" placeholder="Opsi" type="text" igroup-size="md" />
                        </td>
                        <td>
                          <button onclick="trigger(${i})" type='button' class="btn btn-xs btn-default text-success mx-1 shadow btn-add" title="Tambah">
                            <i class="fa fa-lg fa-fw fa-plus"></i>
                            Jawaban
                          </button>
                        </td>
                    </tr>
                    <tr id="btn${i}"></tr>
                    <tr>
                        <td>
                            <x-adminlte-input id="${i+1}" name="${name}_option[]" label="Opsi" placeholder="Opsi" type="text" igroup-size="md" />
                        </td>
                        <td>
                          <button onclick="trigger(${i+1})" type='button' class="btn btn-xs btn-default text-success mx-1 shadow btn-add" title="Tambah">
                            <i class="fa fa-lg fa-fw fa-plus"></i>
                            Jawaban
                          </button>
                        </td>
                    </tr>
                    <tr id="btn${i+1}"></tr>
                    `);
                    ++i;
                } else {
                    $(`#option_field${id}`).fadeOut('slow');
                    $(`#option${id}`).children('tr').remove();
                }
            });
            $(`#option_button${id}`).click(function() {
                ++i;
                $(`#option${id}`).append(`
                    <tr>
                        <td>
                            <x-adminlte-input id="${i}"  name="${id}_option[]" label="Opsi" placeholder="Opsi" type="text" igroup-size="md" />
                        </td>
                        <td>
                            <button onclick="trigger(${i})" type='button' class="btn btn-xs btn-default text-success mx-1 shadow btn-add" title="Tambah">
                              <i class="fa fa-lg fa-fw fa-plus"></i>
                              Jawaban
                            </button>
                            <button type="button" class="btn btn-xs btn-default text-danger mx-1 shadow remove-field" title="Delete">
                              <i class="fa fa-lg fa-fw fa-trash"></i>
                              Delete
                            </button>
                        </td>
                    </tr>
                    <tr id="btn${i}"></tr>
                    `);
            });
            $(document).on('click', '.remove-field', function() {
                --i;
                $(this).parent('td').parent('tr').remove();
            });
        }
    </script>
@stop
