<?php

  /**
    +----------------------------------------------------------
    *    DataHandler
    +----------------------------------------------------------
    *   处理有关实例化缓存的请求
    *   
    +----------------------------------------------------------
    *  @author     Wenson
    *  @version    2012-12-26
    *  @package    DataObj
    +----------------------------------------------------------
    */
  class DataHandler 
  {
      /**
       +----------------------------------------------------------
       * 枚举要实例化的类，每当添加新Cache时，要向此加入枚举值
       +----------------------------------------------------------
       * @param string $name     Cache的名称
       * @param int $gameuid     玩家编号
       * @param int $server      逻辑服
       * @return Instance
       +----------------------------------------------------------
       */
      private static function getInstance($name,$gameuid,$server)
      {
          include_once $name . '.php';
          
          switch ($name)
          {
              case 'ChatCache':
                  return new ChatCache($gameuid,$server);
                  break;
              case 'AccountCache':
                  return new AccountCache($gameuid,$server);
                  break;
              default:break;
          }
      }
  }

?>