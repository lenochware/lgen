{
	"first": {
		"name": "The Town",
		"type": "regular",
		"base-tile": "earth_wall",
		"tags": ["large"],
		"exits": [
			{"id": "small-cave", "tile": "stairs-down"},
			{"id": "wilderness", "tile": "stairs-down"}
		]
	},

	"small-cave": {
		"name": "Small cave",
		"type": "regular",
		"tags": [],
		"exits": [
			{"id": "first", "tile": "stairs-up"}
		],
		
 		"dependencies": {
			"small": { "empty": 0.5, "crap": 0.5, "center-pillar": 0.2, "monsters": -0.1, "doors-open": 0.5 },
			"large": {"monsters": 0.2, "decor": 0.2, "passage": 0.5},
			"huge": {"special": 0.7, "monsters": 0.3, "decor": 0.2},
			"corridor": {"monsters": -0.1, "trap": 0.3, "obstacle": 0.1 },
			"dead-end": {"monsters": 0.5, "treasure": 0.5, "crap": 0.3},
			"crossroad": {"treasure": 0.2, "crap": 0.3},
			"secret": {"special": 0.7, "valuables": 0.7, "treasure": 0.5},
			"center-pillar": {"monster": 0.5, "item": 0.5, "hidden": 0.5},
			"arena-border": { "walls": 1, "passage": -1 },
			"arena": { "walls": 0.5, "passage": -1, "special": 0.5 }
		},

		"paint": {
			"monsters": {"painter": "random", "id": "rat"},
			"treasure": {"painter": "random", "id": "copper_coins", "maxcount": 3},
			"decor": {"painter": "random", "id": "shallow_water"},
			"crap": {"painter": "random", "id": ["slime", "dust", "rock"] },
			"obstacle": {"painter": "random", "id": "rubble"},
			"center-pillar": {"painter": "random", "id": "wall", "pos": "center"},
			"item": {"painter": "random", "id": "small_shield"},
			"valuables": {"painter": "random", "id": "bread"},
			"walls": 	{"painter": "fill", "id": "wall", "pos": "walls"},
			"forest": {"painter": "simplex", 
				//"id": ["none", "shallow_water", "high_grass", "tree"],
				"id": ["none", "none", "none", "shallow_water"],
				"weights": [0.2, 0.4, 0.6]
				//"size": [6,3]
				,"where": "floor"
			}
			,"special": {"painter": "pattern", "pattern": [["tree", "floor"], ["floor", "tree"]]}
			//,"special": {"painter": "pattern", "pattern": [["wall", "floor"], ["floor", "floor"]]}
			//,"special": {"painter": "maze", "id": "wall"}

			// ,"special": [
			// 	{"painter": "mfill", "id": ["high_grass", "rubble"], "pos": "walls"},
			// 	{"painter": "random", "id": ["snake", "#items.weapon"]}
			// 	]
		}


	},

	"test": {
		"name": "Test level",
		"type": "test",
		"tags": ["small"],
		"max-rooms": 5,
		"exits": []
	},

	"wilderness": {
		"name": "Wilderness",
		"type": "regular",
		"tags": ["big", "huge-rooms", "big-rooms"],
		"exits": [
			{"id": "first", "tile": "stairs-up"}
		]
	}


}