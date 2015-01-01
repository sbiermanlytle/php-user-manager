//
//  ProfileViewController.m
//  UserManager
//
//  Created by Sebastian Bierman-Lytle on 12/28/14.
//  Copyright (c) 2014 iio interactive. All rights reserved.
//

#import "ViewProfileController.h"
#import "EditProfileController.h"

@interface ViewProfileController ()

@end

@implementation ViewProfileController{
    NSString *email;
    NSString *username;
    NSString *name;
    NSString *created;
}

- (void)viewDidLoad {
    [super viewDidLoad];
    
    self.title = @"Profile";
    
    UIBarButtonItem *edit_btn = [[UIBarButtonItem alloc]
                                   initWithTitle:@"Edit"
                                   style:UIBarButtonItemStyleDone
                                   target:self
                                   action:@selector(nav_edit_profile)];
    self.navigationItem.rightBarButtonItem = edit_btn;
    
    self.view.backgroundColor = [UIColor whiteColor];
    
    self.tableView = [[UITableView alloc] initWithFrame:CGRectMake(0, self.ny, self.sw, self.sh-self.ny) style:UITableViewStylePlain];
    self.tableView.dataSource = self;
    self.tableView.delegate = self;
    self.tableView.backgroundView = nil;
    [self.tableView setBackgroundColor:[UIColor whiteColor]];
    [self.view addSubview:self.tableView];
}

-(void)viewWillAppear:(BOOL)animated
{
    email = [[NSUserDefaults standardUserDefaults] stringForKey:@"email"];
    NSArray *user_data = [[NSUserDefaults standardUserDefaults] objectForKey:@"user_data"];
    username = [user_data objectAtIndex:2];
    name = [user_data objectAtIndex:3];
    created = [user_data objectAtIndex:4];
    [self.tableView reloadData];
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (void)nav_edit_profile
{
    EditProfileController *vc = [[EditProfileController alloc] init];
    [self.navigationController pushViewController:vc animated:YES];
}

#pragma mark - Table view data source

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView
{
    return 1;
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    return 5;
}

-(CGFloat) tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath
{
    return 60;
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    
    static NSString *CellIdentifier = @"Cell";
    UITableViewCell *cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleValue1 reuseIdentifier:CellIdentifier];
    if(indexPath.row==0){
        
        cell.textLabel.text = @"Email";
        cell.detailTextLabel.text = email;
        
    } else if (indexPath.row==1){
        
        cell.textLabel.text = @"Name";
        cell.detailTextLabel.text = name;
        
    } else if (indexPath.row==2){
        
        cell.textLabel.text = @"Username";
        cell.detailTextLabel.text = username;
        
    } else if (indexPath.row==3){
        
        cell.textLabel.text = @"Created";
        cell.detailTextLabel.text = created;
        
    } else if (indexPath.row==4){
        
        cell.textLabel.text = @"Logout";
        
    }
    return cell;
}

/*
 // Override to support conditional editing of the table view.
 - (BOOL)tableView:(UITableView *)tableView canEditRowAtIndexPath:(NSIndexPath *)indexPath
 {
 // Return NO if you do not want the specified item to be editable.
 return YES;
 }
 */

/*
 // Override to support editing the table view.
 - (void)tableView:(UITableView *)tableView commitEditingStyle:(UITableViewCellEditingStyle)editingStyle forRowAtIndexPath:(NSIndexPath *)indexPath
 {
 if (editingStyle == UITableViewCellEditingStyleDelete) {
 // Delete the row from the data source
 [tableView deleteRowsAtIndexPaths:@[indexPath] withRowAnimation:UITableViewRowAnimationFade];
 }
 else if (editingStyle == UITableViewCellEditingStyleInsert) {
 // Create a new instance of the appropriate class, insert it into the array, and add a new row to the table view
 }
 }
 */

/*
 // Override to support rearranging the table view.
 - (void)tableView:(UITableView *)tableView moveRowAtIndexPath:(NSIndexPath *)fromIndexPath toIndexPath:(NSIndexPath *)toIndexPath
 {
 }
 */

/*
 // Override to support conditional rearranging of the table view.
 - (BOOL)tableView:(UITableView *)tableView canMoveRowAtIndexPath:(NSIndexPath *)indexPath
 {
 // Return NO if you do not want the item to be re-orderable.
 return YES;
 }
 */

#pragma mark - Table view delegate

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{
    [tableView deselectRowAtIndexPath:indexPath animated:true];
    if(indexPath.row==4)
        [self logout];
}

@end
