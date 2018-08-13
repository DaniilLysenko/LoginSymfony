<?php

namespace App\Controller;

use DateTime;
use Redmine\Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function loginAction(AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        return $this->render('user/login.html.twig', array(
            'error' => $error,
        ));
    }

    /**
     * @Route("/", name="main", methods={"GET"})
     */
    public function index()
    {
        return $this->render('user/main.html.twig');
    }

    /**
     * @Route("/redmine", methods={"GET"})
     */
    public function redmine()
    {
        $amount = 12500;
        $userId = 58;

        $client = new Client('https://redmine.ekreative.com', '20c4fc9e00db3e5f138aaff5558a90011b0b2b56');
        $date = date('Y-m-d', strtotime("first day of this month"));
        $result = $client->time_entry->all([
            'spent_on' => '>='.$date,
            'user_id' => $userId,
            'limit' => 300
        ]);

        $performer = $client->user->show($userId);
        $total = $amount;
        $count = count($result['time_entries']);
        if (count($result['time_entries']) > 10) $count = random_int(7, 10);

        $random_ids = [];
        $tasks = [];

        $time = 0;

        $r_tasks = [];

        while(count($random_ids) < $count) {
            $r = random_int(0, count($result['time_entries']) - 1);
            if (!in_array($r, $random_ids)) {
                if (isset($result['time_entries'][$r]['issue'])) {
                    $issue = $client->issue->show($result['time_entries'][$r]['issue']['id']);
                    $issue = $issue['issue']['subject'];
                }
                else {
                    $issue = $result['time_entries'][$r]['project']['name'];
                }
                if (!array_key_exists($issue, $r_tasks)) {
                    $r_tasks[$issue] = ['hours' => $result['time_entries'][$r]['hours']];
                } else {
                    $r_tasks[$issue]['hours'] += $result['time_entries'][$r]['hours'];
                }
                $time += $result['time_entries'][$r]['hours'];
                $random_ids[] = $r;
            }
        }

        $price_hour = round($amount / $time, 2);
        $current_amount = 0;

        $index = 0;
        foreach ($r_tasks as $key => $task) {
            $price = $task['hours'] * $price_hour;
            $current_amount += $price;
            if ($index < $count - 1) {
                $price_hour -= round($price_hour * 0.05, 1);
            } else {
                $price += ($total - $current_amount);
            }
            $amount -= floor($price);
            $tasks[] = ['issue' => $key, 'count' => 1, 'price' => $price];
        }

        return $this->render('redmine/index.html.twig',['tasks' => $tasks, 'performer' => $performer['user'],
            'total' => $total]);
    }
}
