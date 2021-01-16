<?php 
namespace time;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\Player;
use pocketmine\event\player\PlayerInteractEvent;

class Main extends PluginBase implements Listener{
	public $playerList = [];
	public $interactDelay = [];
	
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
	public function onCommand(CommandSender $sender,Command $command,string $label,array $args): bool{
		switch ($command->getName()){
			case "test":
				if($sender instanceof Player)
				{
					$this->form($sender);
				}
		}
		return true;
	}
	
	public function onInteract(PlayerInteractEvent $event){
		$player = $event->getPlayer();
		//아이템 아이디를 얻어 오기 위한것
		$item = $event -> getItem();
		
		$action = $event->getAction(4);
		
		if($item->getId() == 280){
			if(!isset($this->interactDelay[$player->getName()])){
				$this->interactDelay[$player->getName()] = time()+1; //1299 예를 들면 이것은 Id 값
				$this->form($player);
			} else {
				if(time() >= $this->interactDelay[$player->getName()]) // 1300>=1299
				{
					unset($this->interactDelay[$player->getName()]);
				}
			}
		}
	}
	
	public function form($player){
		$list = [];
		
		foreach ($this->getServer()->getOnlinePlayers() as $p){
			$list[] = $p -> getName();
		}
		//자기 이름의 배열에 리스트가 생김
		$this->playerList[$player->getName()] = $list; 
		
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$form = $api->createCustomForm(function (Player $player, array $data = null){
			$serv = $player->getServer();
			if($data == null){
				return true;
			}
			$index = $data[1];
			$playerName = $this->playerList[$player->getName()][$index];
			$serv->getPlayer($playerName)->setGamemode($data[2]);
			$player->sendMessage("모드 변경 완료$data[2]");
		});
		
		$form->setTitle("title Here");
		$form->addLabel("Label Here");
		$form->addDropdown("DropDwon", $this->playerList[$player->getName()]);
		$form->addSlider("겜모 변경", 0,3);
		$form->sendToPlayer($player);
		return $form;
	}
		
}
?>