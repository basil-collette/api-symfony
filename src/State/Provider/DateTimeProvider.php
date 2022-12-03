<?php
namespace App\State;

use Faker\Provider\Base as BaseProvider;
use DateTimeImmutable;

final class DatetimeProvider extends BaseProvider
{

	public function dateTimeToDateTimeImmutable($date): DateTimeImmutable
	{		
		/*
		if (!$date instanceof DateTime) {
			throw new InvalidArgumentException('Expected instance of DateTime');
		}
		*/

        return DateTimeImmutable::createFromMutable($date);
	}

}