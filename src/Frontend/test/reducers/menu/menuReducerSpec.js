/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian@mbosch.me>
 * (c) Ben Bieler <ben@benbieler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import menuReducer         from '../../../reducers/menu/menuReducer';
import { expect }          from 'chai';
import { TRANSFORM_ITEMS } from '../../../constants/Menu';

describe('menuReducer', () => {
  const exampleData = [
    {
      id:        'protected item',
      role:      'ROLE_ADMIN',
      logged_in: true
    },
    {
      id:     'public item',
      portal: true
    },
    {
      id:        'internal',
      logged_in: true
    }
  ];

  it('filters menu items for authenticated users with full admin rights', () => {
    expect(menuReducer([], { items: exampleData, type: TRANSFORM_ITEMS, authData: { is_admin: true, logged_in: true } })).to.deep.equal([
      {
        id:        'protected item',
        role:      'ROLE_ADMIN',
        logged_in: true
      },
      {
        id:        'internal',
        logged_in: true
      }
    ]);
  });

  it('filters menu items for non-authenticated users', () => {
    expect(menuReducer([], { items: exampleData, type: TRANSFORM_ITEMS, authData: { is_admin: false, logged_in: false } })).to.deep.equal([
      {
        id:     'public item',
        portal: true
      }
    ]);
  });

  it('filters menu items for authenticated users without admin rights', () => {
    expect(menuReducer([], { items: exampleData, type: TRANSFORM_ITEMS, authData: { is_admin: false, logged_in: true } })).to.deep.equal([
      {
        id:        'internal',
        logged_in: true
      }
    ]);
  });
});
