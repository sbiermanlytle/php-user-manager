//
//  ProfileViewController.h
//  UserManager
//
//  Created by Sebastian Bierman-Lytle on 12/28/14.
//  Copyright (c) 2014 iio interactive. All rights reserved.
//

#import "BaseController.h"

@interface ViewProfileController : BaseController <UITableViewDataSource, UITableViewDelegate>

@property (strong,nonatomic) UITableView* tableView;

@end
