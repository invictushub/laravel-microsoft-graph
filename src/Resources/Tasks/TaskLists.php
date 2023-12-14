<?php

namespace Invictushub\MsGraph\Resources\Tasks;

use Invictushub\MsGraph\Facades\MsGraph;

class TaskLists extends MsGraph
{
    public function get(array $params = []): MsGraph
    {
        $params = http_build_query($params);

        return MsGraph::get('me/todo/lists?'.$params);
    }

    public function find(string $listId): MsGraph
    {
        return MsGraph::get("me/todo/lists/$listId");
    }

    public function store(string $name): MsGraph
    {
        return MsGraph::post('me/todo/lists', [
            'displayName' => $name,
        ]);
    }

    public function update(string $listId, string $name): MsGraph
    {
        return MsGraph::patch("me/todo/lists/$listId", [
            'displayName' => $name,
        ]);
    }

    public function delete(string $listId): MsGraph
    {
        return MsGraph::delete("me/todo/lists/$listId");
    }
}
