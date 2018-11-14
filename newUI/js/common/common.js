$(function(){
  // ul-li-com
  $('.ul-li-com>p').click(function () {
    $(this).next('ul').toggle()
  })
  $('.ul-li-com>ul>li').click(function(){
    $(this).parent().hide()
    $(this).parent().prev().text($(this).text())
  })
  //卡片展开和收起
  setTimeout(function () {
      $('.content-wrap-expand').click(function(){
          var wrapperElem = $(this).parent().parent().parent()
          var contentElem = wrapperElem.find('.wrap-content')
          if($(this).children('span').text()=='收起'){
              $(this).children('span').text('展开')
              $(this).children('i').addClass('icon-shouqizhankai').removeClass('icon-shouqizhankai1')
              if (contentElem.length) {
                  contentElem.css('display', 'none')
              }
              wrapperElem.css({ 'height': '70px','overflow':'hidden'})
          }else{
              $(this).children('span').text('收起')
              $(this).children('i').addClass('icon-shouqizhankai1').removeClass('icon-shouqizhankai')
              if (contentElem.length) {
                  contentElem.css('display', 'block')
              }
              wrapperElem.css({ 'height': 'unset', 'overflow': 'unset'})
          }
      })
      $('.z-card').each(function() {
          if ($(this).hasClass('in-fold')) {
              $(this).find('.content-wrap-expand').click()
          }
      })
  })

})
