<?php
namespace App\Telegram\CallbackCommands;

use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;

/**
 * Class CallbackCommand
 *
 * @method mixed replyWithMessage($use_sendMessage_parameters)       Reply Chat with a message. You can use all the sendMessage() parameters except chat_id.
 * @method mixed replyWithPhoto($use_sendPhoto_parameters)           Reply Chat with a Photo. You can use all the sendPhoto() parameters except chat_id.
 * @method mixed replyWithAudio($use_sendAudio_parameters)           Reply Chat with an Audio message. You can use all the sendAudio() parameters except chat_id.
 * @method mixed replyWithVideo($use_sendVideo_parameters)           Reply Chat with a Video. You can use all the sendVideo() parameters except chat_id.
 * @method mixed replyWithVoice($use_sendVoice_parameters)           Reply Chat with a Voice message. You can use all the sendVoice() parameters except chat_id.
 * @method mixed replyWithDocument($use_sendDocument_parameters)     Reply Chat with a Document. You can use all the sendDocument() parameters except chat_id.
 * @method mixed replyWithSticker($use_sendSticker_parameters)       Reply Chat with a Sticker. You can use all the sendSticker() parameters except chat_id.
 * @method mixed replyWithLocation($use_sendLocation_parameters)     Reply Chat with a Location. You can use all the sendLocation() parameters except chat_id.
 * @method mixed replyWithChatAction($use_sendChatAction_parameters) Reply Chat with a Chat Action. You can use all the sendChatAction() parameters except chat_id.
 *
 * @package App\Telegram\CallbackCommands
 */
abstract class CallbackCommand
{
    protected $name;

    /** @var Update */
    protected $update;

    /** @var Api */
    protected $telegram;

    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
    }

    public function getTelegram()
    {
        return $this->telegram;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setUpdate(Update $update)
    {
        $this->update = $update;

        return $this;
    }

    public function getUpdate()
    {
        return $this->update;
    }

    public function getCallbackData()
    {
        $data = $this->getName() . ':' . implode(",", $this->getParameters());

        if (strlen($data) > 64) {
            throw new \InvalidArgumentException("Callback data is larger than 64 bytes");
        }

        return $data;
    }

    public function __call($method, $arguments)
    {
        $action = substr($method, 0, 9);
        if ($action !== 'replyWith') {
            throw new \BadMethodCallException("Method [$method] does not exist.");
        }

        $reply_name = studly_case(substr($method, 9));
        $methodName = 'send' . $reply_name;

        if (!method_exists($this->telegram, $methodName)) {
            throw new \BadMethodCallException("Method [$method] does not exist.");
        }

        if (null === $this->update->getCallbackQuery() || null === $chat = $this->update->getCallbackQuery()->getMessage()->getChat()) {
            throw new \BadMethodCallException("No chat available for reply with [$method].");
        }

        $chat_id = $chat->getId();

        $params = array_merge(compact('chat_id'), $arguments[0]);

        return call_user_func_array([$this->telegram, $methodName], [$params]);
    }

    public function editMessageText($params)
    {
        $callbackQuery = $this->update->getCallbackQuery();
        if (null === $callbackQuery || null === $callbackQuery->getMessage()) {
            throw new \BadMethodCallException("No callbackQuery available for editMessageText");
        }

        if (!empty($callbackQuery->getInlineMessageId())) {
            $additionalParams = [
                'inline_message_id' => $callbackQuery->getInlineMessageId(),
            ];
        } else {
            $additionalParams = [
                'chat_id' => $callbackQuery->getMessage()->getChat()->getId(),
                'message_id' => $callbackQuery->getMessage()->getMessageId(),
            ];
        }

        $params = array_merge($additionalParams, $params);

        $this->telegram->editMessageText($params);
    }

    public function answerCallbackQuery($params = [])
    {
        if (!$this->getUpdate()->getCallbackQuery()) {
            throw new \BadMethodCallException("CallbackQuery is missing in request");
        }

        $params = array_merge(
            [
                'callback_query_id' => $this->getUpdate()->getCallbackQuery()->getId(),
            ],
            $params
        );

        $this->getTelegram()->answerCallbackQuery($params);
    }

    abstract public function getParameters();

    abstract public function setParameters($params);

    abstract public function handle();
}
