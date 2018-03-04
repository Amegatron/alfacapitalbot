<?php
namespace App\Telegram\Commands;

use App\Opif;
use App\UserPifAmount;
use Telegram\Bot\Commands\Command;

class SetMyCommand extends Command
{
    protected $name = 'setmy';

    protected $description = 'Устанавливает Ваше кол-во паев указанного ПИФа (/setmy <#ПИФа> <кол-во>)';

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {
        if (!preg_match('~^(\d+) (\d+(?:\.\d+)?)$~is', $arguments, $matches)) {
            $this->replyWithMessage(['text' => $this->description]);
            return;
        }

        $pifId = (int)$matches[1];
        $opif = Opif::find($pifId);
        if (!$opif) {
            $this->replyWithMessage(['text' => 'ПИФ с таким номером не найден']);
            return;
        }

        $amount = (double)$matches[2];

        $userId = $this->update->getMessage()->getFrom()->getId();

        $existing = UserPifAmount::where('user_id', '=', $userId)
            ->where('opif_id', '=', $pifId)
            ->first();

        $action = null;
        if ($existing) {
            if ($amount > 0) {
                $existing->amount = $amount;
                $existing->save();
                $action = 'update';
            } else {
                $existing->delete();
                $action = 'remove';
            }
        } else {
            if ($amount > 0) {
                $existing = UserPifAmount::create([
                    'user_id' => $userId,
                    'opif_id' => $pifId,
                    'amount' => $amount,
                ]);
                $action = 'insert';
            } else {
                $action = 'none';
            }
        }

        $message = "Кол-во паев для ПИФа \"{$opif->fullName}\" ";
        if ($action == 'update') {
            $message .= "обновлено до значения ";
        } else if ($action == 'insert') {
            $message .= "установлено в ";
        }
        $message .= $amount;

        if ($action == 'remove') {
            $message = 'Данные о кол-ве паев были удалены';
        }

        if ($action != 'none') {
            $this->replyWithMessage(['text' => $message]);
        }
    }
}