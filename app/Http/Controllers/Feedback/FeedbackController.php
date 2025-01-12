<?php

namespace App\Http\Controllers\Feedback;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\UseCases\AcceptFeedbackData;
use App\UseCases\FeedbackUseCases;
use DateTime;
use DateTimeZone;
use Illuminate\Http\JsonResponse;

class FeedbackController extends Controller
{

    public const timeValue = 1000;

    public function store(FeedbackStoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        $feedback = app(FeedbackUseCases::class)->acceptFeedback(new AcceptFeedbackData(
            title: $data['title'],
            description: $data['description'],
            datetime: $this->convertTimestampToDT($data['datetime']),
            service: $data['service'],
            rating: (int)$data['rating'],
        ));

        return response()->json([
            'id' => $feedback->id
        ], 201);
    }

    public function show(Feedback $feedback): JsonResponse
    {
        return response()->json([
            'title' => $feedback->title,
            'description' => $feedback->description,
            'datetime' => DateTime::createFromFormat('Y-m-d H:i:s',
                    $feedback->datetime)->getTimestamp() * $this::timeValue,
            'service' => $feedback->service,
            'rating' => $feedback->rating,
        ]);
    }

    private function convertTimestampToDT($microtime): DateTime
    {
        $dt = DateTime::createFromFormat('U', floor($microtime / $this::timeValue));
        $dt->setTimeZone(new DateTimeZone('Europe/Moscow'));
        return $dt;
    }
}
