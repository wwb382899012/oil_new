$(function() {
  $('.main_body').width(window.innerWidth - 270 + 'px')
  // 菜单显示与隐藏
  $('.treeview a').click(function(){
    $(this).next().toggle()
  })
  // 菜单栏展开与隐藏
  // isShrink => 1:展开；2:收起
  var isShrink = sessionStorage.getItem('isShrink')
  if (!isShrink) {
    sessionStorage.setItem('isShrink', '1')
    isShrink = '1'
  } else if (isShrink == '2'){
    $('.main-sidebar').addClass('shrink')
    $('header .icon-menu-unfold').removeClass('icon-menu-unfold').addClass('icon-menu-fold')
  }
  $('header .icon-menu-unfold,header .icon-menu-fold').click(function () {
    if (isShrink == '1') {
      sessionStorage.setItem('isShrink', '2')
      isShrink = '2'
    } else if (isShrink == '2') {
      sessionStorage.setItem('isShrink', '1')
      isShrink = '1'
    }
    $('.main-sidebar').toggleClass('shrink')
    $(this).toggleClass('icon-menu-unfold').toggleClass('icon-menu-fold')
  })

  // 在收缩状态下，鼠标悬停在菜单上时
  $('.main-sidebar').mouseover(function () {
    $(this).removeClass('shrink')
  }).mouseleave(function () {
    if (isShrink == '2') {
      $(this).addClass('shrink')
    }
  })
})
