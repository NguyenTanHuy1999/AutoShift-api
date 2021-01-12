@extends('layouts.app')

@section('content')
    <h3>Danh sách sinh viên</h3>
    <a href="/api/students/create-form" class="float-right mb-2 btn btn-primary">Thêm</a>
    <table class="table table-striped">
        <thead class="thead-dark">
        <tr>
            <th scope="col">#</th>
            <th scope="col">MSSV</th>
            <th scope="col">Họ và tên</th>
            <th scope="col">Khóa</th>
            <th scope="col"></th>
        </tr>
        </thead>
        <tbody>
        @foreach($students as $index => $student)
            <tr>
                <th scope="row">{{$index}}</th>
                <td>{{$student->identification_num}}</td>
                <td>{{$student->full_name}}</td>
                <td>{{$student->course_name}}</td>
                <td>
                    <button type="button" class="btn btn-danger float-right" data-toggle="modal" data-target="#btnDeleteItem{{$student->id}}">
                        Xóa
                    </button>
                    <!-- Modal -->
                    <div class="modal fade" id="btnDeleteItem{{$student->id}}" tabindex="-1" role="dialog" aria-labelledby="btnDeleteItem{{$student->id}}Label" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Thông báo</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    Bạn có muốn xáo sinh viên {{$student->full_name}}?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                                    <a href="/api/students/delete?id={{$student->id}}" class="btn btn-danger">Xóa</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
