<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name') }}</title>
        <link rel="stylesheet" href="{{ asset('vendor/bootstrap.min.css') }}"/>
        <link rel="stylesheet" href="{{ asset('vendor/jqx.base.css') }}" />
        <link rel="stylesheet" href="{{ asset('vendor/dropzone.min.css') }}" />
        <style>
            .maindiv{
                width: 100%,
            }
            #kanban{
                width: 100%,
            }
        </style>
    </head>
    <body>
        <div class="maindiv">
            <h1>{{ config('app.name') }}</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
                Add New Task
            </button>
            <!-- Add Modal -->
            <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addItemModalLabel">Add New Task</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="addTaskForm">
                                <div class="form-group mb-2">
                                    <label class="form-label">Name</label>
                                    <input type="text" id="name" class="form-control">
                                </div>
                                <div class="form-group mb-2">
                                    <label class="form-label">Description</label>
                                    <textarea id="description" class="form-control" cols="30" rows="3"></textarea>
                                </div>
                                <div class="form-group mb-2">
                                    <label class="form-label">Priority</label>
                                    <select id="priority" class="form-control">
                                        <option value="low" selected>Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Image</label>
                                    <div class="dropzone"></div>
                                    <input type="hidden" id="image_name">
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="addItemFormBtn">Add Task</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Update Modal -->
            <div class="modal fade" id="updateItemModal" tabindex="-1" aria-labelledby="updateItemModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="updateItemModalLabel">
                                Update Task
                                <button class="btn btn-sm btn-danger" id="taskDeleteBtn">Delete</button>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="updateTaskForm">
                                <input type="hidden" id="update_id">
                                <div class="form-group mb-2">
                                    <label class="form-label">Name</label>
                                    <input type="text" id="update_name" class="form-control">
                                </div>
                                <div class="form-group mb-2">
                                    <label class="form-label">Description</label>
                                    <textarea id="update_description" class="form-control" cols="30" rows="3"></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="updateItemFormBtn">Save Task</button>
                        </div>
                    </div>
                </div>
            </div>
            <div id='kanban'></div>
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script src="{{ asset('vendor/bootstrap.min.js') }}"></script>
        <script src="https://jqwidgets.com/public/jqwidgets/jqx-all.js"></script>
        <script src="{{ asset('vendor/dropzone.min.js') }}"></script>


        <script type="text/javascript">
            function fetchData() {
                let response = $.ajax({
                    url:"{{ route('list') }}",
                    method: "GET",
                    success: function (resp) {
                        initKanban(resp);
                    },
                    error: function (err) {
                        alert("something went wrong");
                    }
                });
            }

            $(document).ready(function () {
                Dropzone.autoDiscover = false;
                const dropzone = new Dropzone(".dropzone", { 
                    url: "{{ route('file-upload') }}",
                    maxFiles:1,
                    success: (file, response) => {
                        $("#image_name").val(response);
                    }
                });

                $('#kanban').on('itemMoved', function (event) {
                    let args = event.args;
                    let itemId = args.itemId;
                    let priority = args.newColumn.dataField;
                    $.ajax({
                        url:"{{ route('update') }}",
                        method: "POST",
                        data: {
                            id: itemId,
                            priority: priority,
                        },
                        success: function (resp) {
                            fetchData();
                        },
                        error: function (err) {
                            alert(err.responseJSON.data);
                        }
                    });
                });

                $('#kanban').on('itemAttrClicked', function (event) {
                    let itemData = event.args.item;
                    $("#update_id").val(itemData.id);
                    $("#update_name").val(itemData.text);
                    $("#update_description").val(itemData.content);
                    $("#updateItemModal").modal('show');
                });
                
                $("#addItemFormBtn").click(function () {
                    let name = $("#name").val();
                    let description = $("#description").val();
                    let priority = $("#priority").val();
                    let image_name = $("#image_name").val();

                    
                    $.ajax({
                        url:"{{ route('store') }}",
                        method: "POST",
                        data: {
                            name: name,
                            description: description,
                            priority: priority,
                            image_name: image_name
                        },
                        success: function (resp) {
                            fetchData();
                            $('#addTaskForm').trigger("reset");
                            $("#addItemModal").modal("hide");
                            dropzone.removeAllFiles(true);
                        },
                        error: function (err) {
                            alert(err.responseJSON.data);
                        }
                    });
                });

                $("#updateItemFormBtn").click(function () {
                    let id = $("#update_id").val();
                    let name = $("#update_name").val();
                    let description = $("#update_description").val();
                    
                    $.ajax({
                        url:"{{ route('update-data') }}",
                        method: "POST",
                        data: {
                            id: id,
                            name: name,
                            description: description,
                        },
                        success: function (resp) {
                            fetchData();
                            $('#updateTaskForm').trigger("reset");
                            $("#updateItemModal").modal("hide");
                        },
                        error: function (err) {
                            alert(err.responseJSON.data);
                        }
                    });
                });

                $("#taskDeleteBtn").click(function () {
                    let id = $("#update_id").val();
                    $.ajax({
                        url:"{{ route('delete') }}",
                        method: "POST",
                        data: {
                            id: id,
                        },
                        success: function (resp) {
                            $('#kanban').jqxKanban('removeItem', id);
                            $('#updateTaskForm').trigger("reset");
                            $("#updateItemModal").modal("hide");
                        },
                        error: function (err) {
                            alert(err.responseJSON.data);
                        }
                    });
                });
            });

            function initKanban(respData){

                var fields = [
                    { name: "id", type: "string" },
                    { name: "status", map: "state", type: "string" },
                    { name: "text", map: "name", type: "string" },
                    { name: "tags", type: "string" },
                    { name: "content", type: "string" },
                    { name: "resourceId", type: "number" }
                ];

                let data = respData.data;
                let images = [];
                
                var source =
                {
                    localData: [],
                    dataType: "array",
                    dataFields: fields
                };

                data.forEach(element => {
                    source.localData.push({ id: element.id, state: element.priority, name: element.name, tags: element.is_completed ? "Completed" : "In Progress",content:element.description ,resourceId: element.id });
                    let tempImgName = element.image_name;
                    images.push({ id: element.id, image: '/photos/'+tempImgName});
                });                

                var dataAdapter = new $.jqx.dataAdapter(source);
                var resourcesAdapterFunc = function () {
                    var resourcesSource =
                    {
                        localData: images,
                        dataType: "array",
                        dataFields: [
                            { name: "id", type: "number" },
                            { name: "image", type: "string" }
                        ]
                    };
                    var resourcesDataAdapter = new $.jqx.dataAdapter(resourcesSource);
                    return resourcesDataAdapter;
                }
                
                $('#kanban').jqxKanban({
                    width:"100%",
                    height: "100%",
                    resources: resourcesAdapterFunc(),
                    source: dataAdapter,
                    columns: [
                        { text: "Low", dataField: "low" },
                        { text: "Medium", dataField: "medium" },
                        { text: "High", dataField: "high" }
                    ]
                });
            }

            fetchData();
        </script>
    </body>
</html>
