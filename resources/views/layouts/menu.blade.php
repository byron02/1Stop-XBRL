<!-- <li class="treeview">
  <a href="#">
    <i class="fa fa-dashboard"></i> <span>General</span>
    <span class="pull-right-container">
      <i class="fa fa-angle-left pull-right"></i>
    </span>
  </a>
  <ul class="treeview-menu">
    <li><a href="#"><i class="fa fa-facebook-square"></i> Social Media Links</a></li>
    <li><a href="#"><i class="fa fa-envelope-o"></i> E-mail Settings</a></li>
    <li><a href="#"><i class="fa fa-user"></i> Account</a></li>
  </ul>
</li> -->

<li class="header">PAGES</li>

<li class="{{ Request::is('admin/homePages*') ? 'active' : '' }}">
    <a href="{!! route('admin.homePages.index') !!}"><i class="fa fa-home"></i><span>Home Page</span></a>
</li>

<li class="{{ Request::is('admin/aboutPages*') ? 'active' : '' }}">
    <a href="{!! route('admin.aboutPages.index') !!}"><i class="fa fa-info-circle"></i><span>Our Firm</span></a>
</li>

<li class="{{ Request::is('admin/practiceAreas*') ? 'active' : '' }}">
    <a href="{!! route('admin.practiceAreas.index') !!}"><i class="fa fa-briefcase"></i><span>Practice Areas</span></a>
</li>

<li class="{{ Request::is('admin/significantCases*') ? 'active' : '' }}">
    <a href="{!! route('admin.significantCases.index') !!}"><i class="fa fa-legal"></i><span>Significant Cases</span></a>
</li>

<li class="header">MODULES</li>

<li class="{{ Request::is('admin/profiles*') ? 'active' : '' }}">
    <a href="{!! route('admin.profiles.index') !!}"><i class="fa fa-user"></i><span>Profiles</span></a>
</li>

<li class="{{ Request::is('admin/testimonials*') ? 'active' : '' }}">
    <a href="{!! route('admin.testimonials.index') !!}"><i class="fa fa-file-text-o"></i><span>Testimonials</span></a>
</li>

<li class="{{ (Request::is('admin/news*') && isset($_GET['search']) && $_GET['search'] == 'type:0') ? 'active' : '' }}">
    <a href="{!! route('admin.news.index', ['search' => 'type:0']) !!}"><i class="fa fa-newspaper-o"></i><span>News</span></a>
</li>

<li class="{{ (Request::is('admin/news*') && isset($_GET['search']) && $_GET['search'] == 'type:1') ? 'active' : '' }}">
    <a href="{!! route('admin.news.index', ['search' => 'type:1']) !!}"><i class="fa fa-newspaper-o"></i><span>RGW In The News</span></a>
</li>

<li class="{{ Request::is('admin/gurveysLaws*') ? 'active' : '' }}">
    <a href="{!! route('admin.gurveysLaws.index') !!}"><i class="fa fa-music"></i><span>Gurvey's Laws</span></a>
</li>