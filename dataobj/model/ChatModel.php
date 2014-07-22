<?php
	class ChatModel extends BaseModel
	{
		protected function getTableName() {
			return 'chat_counter';
		}
		
		protected function getChatFeilds(){
			return 'surplus,chatdate';
		}
		
		protected function getSurplus($gameuid){
			$where = array ('gameuid' => $gameuid);
			$res = $this->hsSelectOne ( $this->getTableName (), $this->gameuid, $this->getChatFeilds(), $where );
			if(empty($res))
			{
				$res = $this->initChatCounter($gameuid);
			}
			return $res;
		}
		
		public function initChatCounter($gameuid){
			$chat = array ('gameuid' => $gameuid, 'surplus' => 1,'chatdate' => date('Ymd',time()));
			$this->hsInsert ( $this->getTableName (), $this->gameuid, $chat );
			return $chat;
		}
		
		public function updateChatCounter($gameuid){
			$where = array ('gameuid' => $gameuid );
			$content = array('chatdate'=>date('Ymd'),'surplus'=>5);
			return $this->hsUpdate ( $this->getTableName (), $this->gameuid, $content, $where );
		}
	}
?>