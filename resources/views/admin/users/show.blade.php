@extends('layouts.admin')
@section('page')
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card mb-3">
                              <div class="card-header">
                                <div class="row">
                                    <div class="col-md-8 col-8 card_title_part">
                                        <i class="fab fa-gg-circle"></i>View User Information
                                    </div>  
                                    <div class="col-md-4 col-4 card_button_part">
                                        <a href="{{url('dashboard/user')}}" class="btn btn-sm btn-dark"><i class="fas fa-th"></i>All User</a>
                                    </div>  
                                </div>
                              </div>
                              <div class="card-body">
                                <div class="row">
                                    <div class="col-md-2"></div>
                                    <div class="col-md-8">
                                        <table class="table table-bordered table-striped table-hover custom_view_table">
                                          <tr>
                                            <td>Name</td>  
                                            <td>:</td>  
                                            <td>{{$user->name}}</td>  
                                          </tr>
                                          <tr>
                                            <td>Phone</td>  
                                            <td>:</td>  
                                            <td>{{$user->phone}}</td>  
                                          </tr>
                                          <tr>
                                            <td>Email</td>  
                                            <td>:</td>  
                                            <td>{{$user->email}}</td>  
                                          </tr>
                                          <tr>
                                            <td>Username</td>  
                                            <td>:</td>  
                                            <td>{{$user->username}}</td>  
                                          </tr>
                                          <tr>
                                            <td>Creator</td>
                                            <td>:</td>
                                            <td>{{$user->creatorUser->name ?? 'Self-Registered'}}</td>
                                          </tr>
                                          <tr>
                                            <td>Role</td>  
                                            <td>:</td>  
                                            <td>{{$user->role->role_name ?? 'N/A'}}</td>  
                                          </tr>
                                          <tr>
                                            <td>Created Time</td>
                                            <td>:</td>
                                            <td>{{date('d M y', strtotime($user->created_at))}}</td>
                                          </tr>
                                          <tr>
                                            <td>Status</td>
                                            <td>:</td>
                                            <td>
                                                @if($user->status == 1)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                          </tr>
                                          <tr>
                                            <td>Editor</td>
                                            <td>:</td>
                                            <td>{{$user->editorInfo->name ?? 'N/A'}}</td>
                                          </tr>
                                          <tr>
                                            <td>Photo</td>  
                                            <td>:</td>  
                                            <td>
                                              @if($user->photo)
                                                <img class="img200" src="{{asset('uploads/users/'. $user->photo)}}" alt=""/>  
                                               @else 
                                                <img class="img200" src="{{asset('contents/admin')}}/images/avatar.jpg" alt="Default"/>  
                                               @endif 
                                            </td>  
                                          </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-2"></div>
                                </div>
                              </div>
                            </div>
                        </div>
                    </div>
@endsection