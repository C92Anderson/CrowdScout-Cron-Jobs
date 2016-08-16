import pandas as pd
import html5lib

teams = ['ATL','MIN','BUF','MIA','NE','NYJ','BAL','CIN','CLE','PIT','HOU','IND','JAX','TEN','DEN','KC','OAK','SD','SEA',
         'SF','RAM','ARZ','TB','NO','CAR','DAL','NYG','PHI','WAS','CHI','DET','GB']

for team in teams:
    url = "http://www.ourlads.com/nfldepthcharts/roster/{}".format(team)
    team_dfs = pd.read_html(url)
    roster = team_dfs[0]
    print roster



url = "http://bruins.nhl.com/club/roster.htm"
team = pd.read_html(url)