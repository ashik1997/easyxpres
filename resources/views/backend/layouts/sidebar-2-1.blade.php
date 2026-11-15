<div class="sidebar">
      <!-- Sidebar user panel (optional) -->
     

      <!-- SidebarSearch Form -->
      
      @php 
      $carousel = Auth::guard('admin')->user()->carousel;
      $category = Auth::guard('admin')->user()->category;
      $sub_category = Auth::guard('admin')->user()->sub_category;
      $brand = Auth::guard('admin')->user()->brand;
      $product = Auth::guard('admin')->user()->product;
      $order_management = Auth::guard('admin')->user()->order_management;
      $ecommerce_setting = Auth::guard('admin')->user()->ecommerce_setting;
      $role_access = Auth::guard('admin')->user()->role_access;
    
   
    @endphp

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          
          <li class="nav-item">
            <a href="{{ URL('admin/dashboard')}}" class="nav-link">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Dashboard
                <!-- <span class="right badge badge-danger">New</span> -->
              </p>
            </a>
          </li>

          <!-- Carousel -->
          @if ($carousel == Null)

          @else

          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-copy"></i>
              <p>
                Carousel/Side Banner
                <i class="fas fa-angle-left right"></i>
                <!-- <span class="badge badge-info right">6</span> -->
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
              <a href="{{ route('list.carousel')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Update Banner Carousel</p>
                </a>
                <a href="{{ route('banner.best.sale')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Update Best Sale Banner</p>
                </a>
                <a href="{{ route('banner.new.arrival')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Update New Arrival Banner</p>
                </a>
                <a href="{{ route('banner.hot.deal')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Update Hot Deal Banner</p>
                </a>
                <a href="{{ route('banner.trendy')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Update Trendy Banner</p>
                </a>
              </li>  
            </ul>
          </li>
{{-- 
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-copy"></i>
              <p>
              Carousel
                <i class="fas fa-angle-left right"></i>
                <!-- <span class="badge badge-info right">6</span> -->
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ route('list.carousel')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>List Carousel</p>
                </a>
              </li>  
            </ul>
          </li> --}}
          @endif

          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-copy"></i>
              <p>
              Super Category
                <i class="fas fa-angle-left right"></i>
                <!-- <span class="badge badge-info right">6</span> -->
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
              <a href="{{route('add.super.category')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Add Super Category</p>
                </a>
                <a href="{{ route('list.super.category')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>List Super Category</p>
                </a>
              </li>  
            </ul>
          </li>

          <!-- Category -->
          @if ($category == Null)

@else
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-copy"></i>
              <p>
              Category
                <i class="fas fa-angle-left right"></i>
                <!-- <span class="badge badge-info right">6</span> -->
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
              <a href="{{route('add.category')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Add Category</p>
                </a>
                <a href="{{ route('list.category')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>List Category</p>
                </a>
              </li>  
            </ul>
          </li>
          @endif

           <!-- Sub Category -->
           @if ($sub_category == Null)

@else
           <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-copy"></i>
              <p>
              Sub Category
                <i class="fas fa-angle-left right"></i>
                <!-- <span class="badge badge-info right">6</span> -->
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
              <a href="{{ route('add.subcategory')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Add Sub Category</p>
                </a>
                <a href="{{ route('list.subcategory')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>List Sub Category</p>
                </a>
              </li>  
            </ul>
          </li>
          @endif

          <!-- Brand -->
          @if ($brand == Null)

@else
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-copy"></i>
              <p>
              Brand
                <i class="fas fa-angle-left right"></i>
                <!-- <span class="badge badge-info right">6</span> -->
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
              <a href="{{ route('add.brand')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Add Brand</p>
                </a>
                <a href="{{ route('list.brand')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>List Brand</p>
                </a>
              </li>  
            </ul>
          </li>
          @endif

          <!-- Product -->
          @if ($product == Null)

@else
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-copy"></i>
              <p>
              Product
                <i class="fas fa-angle-left right"></i>
                <!-- <span class="badge badge-info right">6</span> -->
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
              <a href="{{ route('add.product')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Add Product</p>
                </a>
                <a href="{{ route('list.product')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>List Product</p>
                </a>
              </li>  
            </ul>
          </li>
          @endif

           <!-- Order Management -->
           @if ($order_management == Null)

@else
           <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-copy"></i>
              <p>
              Order Management
                <i class="fas fa-angle-left right"></i>
                <!-- <span class="badge badge-info right">6</span> -->
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
             
                <a href="{{ route('list.order.management') }}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>List Order</p>
                </a>
              </li>  
            </ul>
          </li>
          @endif

         
          <!-- Ecommerce Setting  -->
          @if ($ecommerce_setting == Null)

@else
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-copy"></i>
              <p>
                Ecommerce Settings
                <i class="fas fa-angle-left right"></i>
                <!-- <span class="badge badge-info right">6</span> -->
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ route('list.logo.setting')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>List Logo</p>
                </a>
              </li>  
            </ul>
          </li>
          @endif

          <!-- Employee Role Access  -->
          @if ($role_access == Null)

@else
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-copy"></i>
              <p>
                Employee Role Access
                <i class="fas fa-angle-left right"></i>
                <!-- <span class="badge badge-info right">6</span> -->
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ route('add.role')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Add Role Access</p>
                </a>
              </li>  
            </ul>

            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ route('list.role')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>List Role Access</p>
                </a>
              </li>  
            </ul>
          </li>
          @endif

          <li class="nav-item">
            <a href="{{ route('admin.logout')}}" class="nav-link">
              <i class="nav-icon fas fa-sign-out-alt"></i>
              <p>
                Logout
                <!-- <span class="right badge badge-danger">New</span> -->
              </p>
            </a>
          </li>
         
        
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>