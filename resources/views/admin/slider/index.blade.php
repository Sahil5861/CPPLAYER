@extends('layout.default')
@section('mytitle', 'Slider List')
@section('page', 'Slider / List')
@section('content')
<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            
            <div class="widget-content widget-content-area br-6">
                <div id="delete_bd_ms"></div>
                @if(session()->has('message'))
                    <div class="alert alert-success alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>    
                        <strong>{{ session()->get('message') }}</strong>
                    </div>
                @endif
                <div class="text-right">
                    <a href="{{url('add-slider')}}" class="btn btn-primary mb-2">Add +</a>
                </div>
                <div class="table-responsive mb-4 mt-4">
                    <table id="multi-column-ordering" class="table table-hover" style="width:100%">
                        <thead>
                            <tr>
                                
                                <th>Image</th>
                                <th>Title</th>   
                                <th>Content Type</th>                                                             
                                <th>Status</th>
                                <th>Created Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tableItem">
                            
                            <!-- <tr>
                                <td>
                                    <div class="d-flex">                                                        
                                        <div class="usr-img-frame mr-2 rounded-circle">
                                            <img alt="avatar" class="img-fluid rounded-circle" src="assets/img/90x90.jpg">
                                        </div>
                                        <p class="align-self-center mb-0 admin-name"> Garrett </p>
                                    </div>
                                </td>
                                <td>Accountant</td>
                                <td>Tokyo</td>
                                <td>63</td>
                                <td>2011/07/25</td>
                                <td>$170,750</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex">                                                        
                                        <div class="usr-img-frame mr-2 rounded-circle">
                                            <img alt="avatar" class="img-fluid rounded-circle" src="assets/img/90x90.jpg">
                                        </div>
                                        <p class="align-self-center mb-0 admin-name"> Ashton </p>
                                    </div>
                                </td>
                                <td>Junior Technical Author</td>
                                <td>San Francisco</td>
                                <td>66</td>
                                <td>2009/01/12</td>
                                <td>$86,000</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex">                                                        
                                        <div class="usr-img-frame mr-2 rounded-circle">
                                            <img alt="avatar" class="img-fluid rounded-circle" src="assets/img/90x90.jpg">
                                        </div>
                                        <p class="align-self-center mb-0 admin-name"> Cedric </p>
                                    </div>
                                </td>
                                <td>Senior Javascript Developer</td>
                                <td>Edinburgh</td>
                                <td>22</td>
                                <td>2012/03/29</td>
                                <td>$433,060</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex">                                                        
                                        <div class="usr-img-frame mr-2 rounded-circle">
                                            <img alt="avatar" class="img-fluid rounded-circle" src="assets/img/90x90.jpg">
                                        </div>
                                        <p class="align-self-center mb-0 admin-name"> Airi </p>
                                    </div>
                                </td>
                                <td>Accountant</td>
                                <td>Tokyo</td>
                                <td>33</td>
                                <td>2008/11/28</td>
                                <td>$162,700</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex">                                                        
                                        <div class="usr-img-frame mr-2 rounded-circle">
                                            <img alt="avatar" class="img-fluid rounded-circle" src="assets/img/90x90.jpg">
                                        </div>
                                        <p class="align-self-center mb-0 admin-name"> Brielle </p>
                                    </div>
                                </td>
                                <td>Integration Specialist</td>
                                <td>New York</td>
                                <td>61</td>
                                <td>2012/12/02</td>
                                <td>$372,000</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex">                                                        
                                        <div class="usr-img-frame mr-2 rounded-circle">
                                            <img alt="avatar" class="img-fluid rounded-circle" src="assets/img/90x90.jpg">
                                        </div>
                                        <p class="align-self-center mb-0 admin-name"> Herrod </p>
                                    </div>
                                </td>
                                <td>Sales Assistant</td>
                                <td>San Francisco</td>
                                <td>59</td>
                                <td>2012/08/06</td>
                                <td>$137,500</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex">
                                        <div class="usr-img-frame mr-2 rounded-circle">
                                            <img alt="avatar" class="img-fluid rounded-circle" src="assets/img/90x90.jpg">
                                        </div>
                                        <p class="align-self-center mb-0 admin-name"> Rhona </p>
                                    </div>
                                </td>
                                <td>Integration Specialist</td>
                                <td>Tokyo</td>
                                <td>55</td>
                                <td>2010/10/14</td>
                                <td>$327,900</td>
                            </tr> -->
                            <!-- <tr>
                                <td>
                                    <div class="d-flex">                                                        
                                        <div class="usr-img-frame mr-2 rounded-circle">
                                            <img alt="avatar" class="img-fluid rounded-circle" src="assets/img/90x90.jpg">
                                        </div>
                                        <p class="align-self-center mb-0 admin-name"> Colleen </p>
                                    </div>
                                </td>
                                <td>Javascript Developer</td>
                                <td>San Francisco</td>
                                <td>39</td>
                                <td>2009/09/15</td>
                                <td>$205,500</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex">                                                        
                                        <div class="usr-img-frame mr-2 rounded-circle">
                                            <img alt="avatar" class="img-fluid rounded-circle" src="assets/img/90x90.jpg">
                                        </div>
                                        <p class="align-self-center mb-0 admin-name"> Sonya </p>
                                    </div>
                                </td>
                                <td>Software Engineer</td>
                                <td>Edinburgh</td>
                                <td>23</td>
                                <td>2008/12/13</td>
                                <td>$103,600</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex">                                                        
                                        <div class="usr-img-frame mr-2 rounded-circle">
                                            <img alt="avatar" class="img-fluid rounded-circle" src="assets/img/90x90.jpg">
                                        </div>
                                        <p class="align-self-center mb-0 admin-name"> Jena </p>
                                    </div>
                                </td>
                                <td>Office Manager</td>
                                <td>London</td>
                                <td>30</td>
                                <td>2008/12/19</td>
                                <td>$90,560</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex">                                                        
                                        <div class="usr-img-frame mr-2 rounded-circle">
                                            <img alt="avatar" class="img-fluid rounded-circle" src="assets/img/90x90.jpg">
                                        </div>
                                        <p class="align-self-center mb-0 admin-name"> Quinn </p>
                                    </div>
                                </td>
                                <td>Support Lead</td>
                                <td>Edinburgh</td>
                                <td>22</td>
                                <td>2013/03/03</td>
                                <td>$342,000</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex">                                                        
                                        <div class="usr-img-frame mr-2 rounded-circle">
                                            <img alt="avatar" class="img-fluid rounded-circle" src="assets/img/90x90.jpg">
                                        </div>
                                        <p class="align-self-center mb-0 admin-name"> Charde </p>                                                        
                                    </div>
                                </td>
                                <td>Regional Director</td>
                                <td>San Francisco</td>
                                <td>36</td>
                                <td>2008/10/16</td>
                                <td>$470,600</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex">
                                        <div class="usr-img-frame mr-2 rounded-circle">
                                            <img alt="avatar" class="img-fluid rounded-circle" src="assets/img/90x90.jpg">
                                        </div>
                                        <p class="align-self-center mb-0 admin-name"> Haley </p>
                                    </div>
                                </td>
                                <td>Senior Marketing Designer</td>
                                <td>London</td>
                                <td>43</td>
                                <td>2012/12/18</td>
                                <td>$313,500</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex">
                                        <div class="usr-img-frame mr-2 rounded-circle">
                                            <img alt="avatar" class="img-fluid rounded-circle" src="assets/img/90x90.jpg">
                                        </div>
                                        <p class="align-self-center mb-0 admin-name"> Tatyana </p>
                                    </div>
                                </td>
                                <td>Regional Director</td>
                                <td>London</td>
                                <td>19</td>
                                <td>2010/03/17</td>
                                <td>$385,750</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex">
                                        <div class="usr-img-frame mr-2 rounded-circle">
                                            <img alt="avatar" class="img-fluid rounded-circle" src="assets/img/90x90.jpg">
                                        </div>                                                        
                                        <p class="align-self-center mb-0 admin-name"> Michael </p>
                                    </div>
                                </td>
                                <td>Marketing Designer</td>
                                <td>London</td>
                                <td>66</td>
                                <td>2012/11/27</td>
                                <td>$198,500</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex">
                                        <div class="usr-img-frame mr-2 rounded-circle">
                                            <img alt="avatar" class="img-fluid rounded-circle" src="assets/img/90x90.jpg">
                                        </div>                                                        
                                        <p class="align-self-center mb-0 admin-name"> Paul </p>
                                    </div>
                                </td>
                                <td>Chief Financial Officer (CFO)</td>
                                <td>New York</td>
                                <td>64</td>
                                <td>2010/06/09</td>
                                <td>$725,000</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex">
                                        <div class="usr-img-frame mr-2 rounded-circle">
                                            <img alt="avatar" class="img-fluid rounded-circle" src="assets/img/90x90.jpg">
                                        </div>                                                        
                                        <p class="align-self-center mb-0 admin-name"> Gloria </p>
                                    </div>
                                </td>
                                <td>Systems Administrator</td>
                                <td>New York</td>
                                <td>59</td>
                                <td>2009/04/10</td>
                                <td>$237,500</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex">
                                        <div class="usr-img-frame mr-2 rounded-circle">
                                            <img alt="avatar" class="img-fluid rounded-circle" src="assets/img/90x90.jpg">
                                        </div>                                                        
                                        <p class="align-self-center mb-0 admin-name"> Bradley </p>
                                    </div>
                                </td>
                                <td>Software Engineer</td>
                                <td>London</td>
                                <td>41</td>
                                <td>2012/10/13</td>
                                <td>$132,000</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex">
                                        <div class="usr-img-frame mr-2 rounded-circle">
                                            <img alt="avatar" class="img-fluid rounded-circle" src="assets/img/90x90.jpg">
                                        </div>                                                        
                                        <p class="align-self-center mb-0 admin-name"> Dai </p>
                                    </div>
                                </td>
                                <td>Personnel Lead</td>
                                <td>Edinburgh</td>
                                <td>35</td>
                                <td>2012/09/26</td>
                                <td>$217,500</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex">
                                        <div class="usr-img-frame mr-2 rounded-circle">
                                            <img alt="avatar" class="img-fluid rounded-circle" src="assets/img/90x90.jpg">
                                        </div>                                                        
                                        <p class="align-self-center mb-0 admin-name"> Jenette </p>                                                        
                                    </div>
                                </td>
                                <td>Development Lead</td>
                                <td>New York</td>
                                <td>30</td>
                                <td>2011/09/03</td>
                                <td>$345,000</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex">
                                        <div class="usr-img-frame mr-2 rounded-circle">
                                            <img alt="avatar" class="img-fluid rounded-circle" src="assets/img/90x90.jpg">
                                        </div>                                                        
                                        <p class="align-self-center mb-0 admin-name"> Yuri </p>
                                    </div>
                                </td>
                                <td>Chief Marketing Officer (CMO)</td>
                                <td>New York</td>
                                <td>40</td>
                                <td>2009/06/25</td>
                                <td>$675,000</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex">
                                        <div class="usr-img-frame mr-2 rounded-circle">
                                            <img alt="avatar" class="img-fluid rounded-circle" src="assets/img/90x90.jpg">
                                        </div>                                                        
                                        <p class="align-self-center mb-0 admin-name"> Caesar </p>
                                    </div>
                                </td>
                                <td>Pre-Sales Support</td>
                                <td>New York</td>
                                <td>21</td>
                                <td>2011/12/12</td>
                                <td>$106,450</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex">                                                        
                                        <div class="usr-img-frame mr-2 rounded-circle">
                                            <img alt="avatar" class="img-fluid rounded-circle" src="assets/img/90x90.jpg">
                                        </div>                                                        
                                        <p class="align-self-center mb-0 admin-name"> Doris </p>
                                    </div>
                                </td>
                                <td>Sales Assistant</td>
                                <td>Sidney</td>
                                <td>23</td>
                                <td>2010/09/20</td>
                                <td>$85,600</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex">                                                        
                                        <div class="usr-img-frame mr-2 rounded-circle">
                                            <img alt="avatar" class="img-fluid rounded-circle" src="assets/img/90x90.jpg">
                                        </div>                                                        
                                        <p class="align-self-center mb-0 admin-name"> Angelica </p>                                                        
                                    </div>
                                </td>
                                <td>Chief Executive Officer (CEO)</td>
                                <td>London</td>
                                <td>47</td>
                                <td>2009/10/09</td>
                                <td>$1,200,000</td>
                            </tr> -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Content Type</th>                                
                                <th>Status</th>
                                <th>Created Date</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('footer')

<script>
    $(document).ready(function() { 
      var table = $('#multi-column-ordering').DataTable({ 
            // "aLengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
             "language": {
                "infoFiltered": '' 
            },
     
            // Load data for the table's content from an Ajax source
            "ajax": { 
                "url": "{{route('getSliderList')}}",
                // "type": "POST",
                "data": function ( d ) {
                    console.log(d);
                            // d.parent_cat = $("#parent_cat").val();
                            // d.sub_cat = $("#sub_cat").val();
                            // d.sub_subcat = $("#sub_subcat").val();
                            // d.search_date = $("#publish").val();

                            // alert(d.parent_cat);
                            // alert(d.sub_cat);
                            // alert(d.sub_subcat);
                }
            },

            columns: [
                {data: 'image', name: 'image'},
                // {data: 'email', "render": function (data, type, row) {
                //  return '<a href="/user/' + row.id +'">' + data + '</a>';
                //     },
                // },
                {data: 'title', name: 'title'},                
                {data: 'content_type', name: 'content_type'},                
                {data: 'status', name: 'status'},
                {data: 'created_at', name: 'created_at'},
                
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],

            drawCallback: function (settings) { 
            
                
                updateIcon()
            },
     
            //Set column definition initialisation properties.
            // "columnDefs": [
            //     { 
            //         "className": "select-checkboxes",
            //         "targets": [ 0 ], //first column / numbering column
            //         "orderable": false //set not orderable
                    
            //     },
            // ],
            // "columnDefs": [{
            //     "defaultContent": "-",
            //     "targets": "_all"
            //   }],
            // "aoColumnDefs": [ { "bSortable": false, "aTargets": [ 0,1,2,3] } ]
     
        });

      $("input#search").on("keyup", function (event) {
            if ($('#search').val().length >= 3 || $('#search').val().length == 0) {
                table.draw(), event.preventDefault()
            }
        });
        $("#btn-search").click(function (a) {
            table.draw(), a.preventDefault()
        });

    });

        // $('#multi-column-ordering').DataTable({
        //     "oLanguage": {
        //         "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
        //         "sInfo": "Showing page _PAGE_ of _PAGES_",
        //         "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
        //         "sSearchPlaceholder": "Search...",
        //        "sLengthMenu": "Results :  _MENU_",
        //     },
        //     "stripeClasses": [],
        //     "lengthMenu": [7, 10, 20, 50],
        //     "pageLength": 7,
        //     columnDefs: [ {
        //         targets: [ 0 ],
        //         orderData: [ 0, 1 ]
        //     }, {
        //         targets: [ 1 ],
        //         orderData: [ 1, 0 ]
        //     }, {
        //         targets: [ 4 ],
        //         orderData: [ 4, 0 ]
        //     } ]
        // });
    </script>
<!-- footer script if required -->
@endsection