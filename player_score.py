import sys
print sys.path

import time
import pandas as pd
import pymysql as pg
import numpy as np

conn = pg.connect(host='mysql.crowdscoutsports.com', user='ca_elo_games', password='cprice31!')
cur = conn.cursor()

cur.execute("SELECT player_name, Date, Elo FROM (SELECT player_id, CAST( game_ts AS DATE ) as Date, Elo FROM `nhl_all`.`hockey_elo_v1` WHERE player_id != '' group by player_id, Date) as A INNER JOIN `nhl_all`.hockey_roster_R as B ON A.player_id=B.nhl_id WHERE player_name != ''")

results = cur.fetchall()
results_list = [list(row) for row in results]
#results_table = []
#print(results_list)

###create dataframe
df = pd.DataFrame(results_list, columns=['Player','Date','Elo'])

###set to date and set index
df["Date"] = pd.to_datetime(df["Date"])
df = df.set_index(['Date'])



####create dates - date index
dates = pd.DataFrame(index=pd.date_range('20151001',time.strftime("%Y%m%d"), freq='D'))
#dates = pd.DataFrame(df["Date"].unique())

###select unique list of players
playerList = df['Player'].unique()

###player elo day
eloDay = pd.DataFrame()

for i in playerList:
    player1 = pd.DataFrame(df.loc[df['Player'] == i])
    player1 = dates.join(player1, how='left')
    player1 = player1.fillna(method='ffill')
    player1 = player1.fillna(method='bfill')
    eloDay = pd.concat([eloDay,player1])

###calculate max elo date by day
eloDay['Elo'] = eloDay[['Elo']].astype(float)
eloDayRange = pd.DataFrame(eloDay.groupby(level=0)['Elo'].agg({'EloMax' : np.max,'EloMin' : np.min}))

###merge on daily max to player dataset
eloDayRange = eloDay.merge(eloDayRange, how='left', left_index=True, right_index=True)

###calculate score
eloDayRange['Score'] = ((eloDayRange.Elo - eloDayRange.EloMin) * 100)/ (eloDayRange.EloMax - eloDayRange.EloMin)
eloDayRange['Date'] = eloDayRange.index
eloDayRange = eloDayRange.reset_index(drop=True)
eloDayRange['cron_ts'] = pd.to_datetime(time.strftime("%Y-%m-%d %X"))

###insert into phpmyadmin
conn2 = pg.connect(host='mysql.crowdscoutsports.com', user='ca_elo_games', password='cprice31!',
                   port=3306, db='nhl_all')
eloDayRange.to_sql(con=conn2, name='hockey_daily_elo_py', if_exists='replace', flavor='mysql')

conn.commit()
conn.close()