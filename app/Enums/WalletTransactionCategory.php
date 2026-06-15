<?php

namespace App\Enums;

enum WalletTransactionCategory: string
{
    case Purchase = 'purchase';
    case GiftSent = 'gift_sent';
    case GiftReceived = 'gift_received';
    case Reward = 'reward';
    case Withdrawal = 'withdrawal';
    case Refund = 'refund';
    case Bonus = 'bonus';
    case Adjustment = 'adjustment';
}
