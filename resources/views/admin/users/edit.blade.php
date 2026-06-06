@extends('layouts.admin')
@section('page')

@php
  $myRole = strtolower(Auth::user()->role->role_name ?? '');
@endphp
                    <div class="row">
                        <div class="col-md-12 ">
                            <form method="post" action="{{route('dashboard.user.update', $data->slug)}}" enctype="multipart/form-data">
                              @csrf  
                              <div class="card mb-3">
                                  <div class="card-header">
                                    <div class="row">
                                        <div class="col-md-8 col-8 card_title_part">
                                            <i class="fab fa-gg-circle"></i>Update User Information
                                        </div>  
                                        <div class="col-md-4 col-4 card_button_part">
                                            <a href="{{url('dashboard/user')}}" class="btn btn-sm btn-dark"><i class="fas fa-th"></i>All User</a>
                                        </div>  
                                    </div>
                                  </div>
                                  <div class="card-body">
                                    <input type="hidden" name="id" value="{{$data->id}}">
                                    <input type="hidden" name="slug" value="{{$data->slug}}">
                                      <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label col_form_label">Name<span class="req_star">*</span>:</label>
                                        <div class="col-sm-7">
                                          <input type="text" class="form-control form_control" id="" name="name" value="{{$data->name}}">
                                        </div>
                                      </div>
                                      <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label col_form_label">Phone:</label>
                                        <div class="col-sm-7">
                                          <input type="text" class="form-control form_control" id="" name="phone" value="{{$data->phone}}">
                                        </div>
                                      </div>
                                      <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label col_form_label">Email<span class="req_star">*</span>:</label>
                                        <div class="col-sm-7">
                                          <input type="email" class="form-control form_control" id="" name="email" value="{{$data->email}}">
                                        </div>
                                      </div>
                                      <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label col_form_label">Username<span class="req_star">*</span>:</label>
                                        <div class="col-sm-7">
                                          <input type="text" class="form-control form_control" id="" name="username" value="{{$data->username}}" disabled>
                                        </div>
                                      </div>
                                      <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label col_form_label">User Role<span class="req_star">*</span>:</label>
                                        <div class="col-sm-4">
                                        @php
                                        $roles = App\Models\Role::all();
                                         $myRole = str_replace([' ', '-'], '_', strtolower(trim(optional(Auth::user()->role)->role_name ?? 'user')));
                                        @endphp 
                                      @if($myRole === 'super_admin')
                                <select name="role_id" class="form-control">
                                  <option value="">Select Role</option>
                                  @foreach($roles as $role)
                                    <option value="{{ $role->id }}"
                                      {{ old('role_id', $data->role_id) == $role->id ? 'selected' : '' }}>
                                      {{ $role->role_name }}
                                    </option>
                                  @endforeach
                                </select>
                                @error('role_id') <div class="text-danger">{{ $message }}</div> @enderror
                              @else
                                {{-- Manager/Viewers: role only show (no input) --}}
                                <div class="form-control bg-light">
                                  {{ $data->role->role_name ?? '' }}
                                </div>
                              @endif
                                        </div>
                                      </div>
                                      <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label col_form_label">Status<span class="req_star">*</span>:</label>
                                      <div class="col-sm-4">
                                        <select name="status" class="form-control">
                                              <option value="1" {{ $data->status == 1 ? 'selected' : '' }}>Active</option>
                                              <option value="0" {{ $data->status == 0 ? 'selected' : '' }}>Inactive</option>
                                          </select>
                                          @error('status') <div class="text-danger">{{ $message }}</div> @enderror
                                      </div>
                                      </div>
                                      <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label col_form_label">Photo<span class="req_star">*</span>:</label>
                                      <div class="col-sm-4">
                                      <input type="file" name="photo" accept="image/*">
                                          @error('photo') <div class="text-danger">{{ $message }}</div> @enderror
                                        @if($data->photo)
                                        <img src="{{ asset('uploads/users/'.$data->photo) }}" alt="User Photo" width="70" class="mt-2">
                                        @endif
                                      </div>
                                      </div>
                                  </div>
                                  <div class="card-footer text-center">
                                    <button type="submit" class="btn btn-sm btn-dark">UPDATE</button>
                                  </div>  
                                </div>
                            </form>
                        </div>
                    </div>
    @endsection